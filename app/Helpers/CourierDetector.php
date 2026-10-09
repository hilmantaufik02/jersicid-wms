<?php

namespace App\Helpers;

class CourierDetector
{
    /**
     * Deteksi ekspedisi berdasarkan no pesanan dan prefix resi
     * 
     * Rules:
     * - No pesanan 26062 + resi 0046 = SiCepat
     * - No pesanan 26062 + resi IDS = ID Express
     * - No pesanan 26062 + resi CM/TG = JNE
     * - No pesanan 26062 + resi SPXID/ID = SPX
     * - No pesanan 58466 + resi JX/JO = J&T
     * - No pesanan 2684 + resi LXAD = LEX
     * - No pesanan 2688 + resi JZ = LEX
     * - No pesanan 2767 + resi JNAP = LEX
     */
    public static function detect(string $orderNumber, string $trackingNumber): array
    {
        // Normalize input
        $orderNumber = trim($orderNumber);
        $trackingNumber = strtoupper(trim($trackingNumber));

        // Extract prefix dari tracking number (ambil 2-5 karakter pertama)
        $prefix = substr($trackingNumber, 0, 5);
        $prefix2 = substr($trackingNumber, 0, 2);
        $prefix3 = substr($trackingNumber, 0, 3);
        $prefix4 = substr($trackingNumber, 0, 4);

        // Rules detection
        $rules = [
            // SiCepat
            ['order' => '26062', 'prefixes' => ['0046'], 'courier' => 'SiCepat', 'service' => 'REG'],

            // ID Express
            ['order' => '26062', 'prefixes' => ['IDS'], 'courier' => 'ID Express', 'service' => 'Regular'],

            // JNE
            ['order' => '26062', 'prefixes' => ['CM', 'TG'], 'courier' => 'JNE', 'service' => 'REG'],

            // SPX (Shopee Express)
            ['order' => '26062', 'prefixes' => ['SPXID', 'ID'], 'courier' => 'SPX', 'service' => 'Standard'],

            // J&T
            ['order' => '58466', 'prefixes' => ['JX', 'JO'], 'courier' => 'J&T', 'service' => 'Express'],

            // LEX (Lazada Express) - multiple patterns
            ['order' => '2684', 'prefixes' => ['LXAD'], 'courier' => 'LEX', 'service' => 'Standard'],
            ['order' => '2688', 'prefixes' => ['JZ'], 'courier' => 'LEX', 'service' => 'Standard'],
            ['order' => '2767', 'prefixes' => ['JNAP'], 'courier' => 'LEX', 'service' => 'Standard'],
        ];

        foreach ($rules as $rule) {
            if ($orderNumber === $rule['order']) {
                foreach ($rule['prefixes'] as $rulePrefix) {
                    if (
                        str_starts_with($prefix, $rulePrefix) ||
                        str_starts_with($prefix2, $rulePrefix) ||
                        str_starts_with($prefix3, $rulePrefix) ||
                        str_starts_with($prefix4, $rulePrefix)
                    ) {
                        return [
                            'courier_name' => $rule['courier'],
                            'courier_service' => $rule['service'],
                            'detected' => true,
                        ];
                    }
                }
            }
        }

        return [
            'courier_name' => null,
            'courier_service' => null,
            'detected' => false,
        ];
    }

    /**
     * Get all supported couriers
     */
    public static function getSupportedCouriers(): array
    {
        return [
            'JNE' => ['REG', 'YES', 'OKE', 'JTR'],
            'J&T' => ['Express', 'Economy'],
            'SiCepat' => ['REG', 'BEST', 'GOKIL', 'HALU'],
            'ID Express' => ['Regular', 'Express'],
            'SPX' => ['Standard', 'Instant'],
            'LEX' => ['Standard', 'Hemat'],
            'AnterAja' => ['Regular', 'Express'],
            'Ninja Express' => ['Standard', 'Express'],
            'POS Indonesia' => ['REG', 'PLUS', 'Kilat'],
        ];
    }
}
