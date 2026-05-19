<?php

namespace App\Services;

use InvalidArgumentException;

/**
 * Voir la classe de test PremiumMemberServiceTest pour les specifications de chaque méthode de cette classe.
 */
class PremiumMemberService
{
    /**
     * Génère un rapport complet sur le profil d'un membre.
     * Specifications : 
     * - L'idenitifiant du membre doit être unique et préfixé par "usr_"
     * - Le nom d'utilisateur doit être nettoyé (trim et lowercase) et placé dans le champs meta.clean_name
     * - L'âge doit être supérieur ou égal à 18 ans
     * - Les centre d'interet doivent être convertis en lowercase et comptés dans le champs preferences.count
     * - La valeur de retour doit être un tableau associatif contenant les champs suivants : id, meta, preferences, status et created_at
     *    - id : un string unique préfixé par "usr_"
     *    - meta : un tableau associatif contenant les champs username, clean_name et age
     *    - preferences : un tableau associatif contenant les champs interests (tableau des centres d'intérêt en lowercase) et count (le nombre de centre d'intérêt)
     *    - status : doit être égal à "active"
     *    - created_at : doit être la date du jour au format Y-m-d
     * 
     * @param string $username Le nom d'utilisateur du membre par exemple : "Billy"
     * @param int $age L'âge du membre par exemple : 26
     * @param array $interests Les centres d'intérêt du membre exemple : ['Coding', 'Gaming', 'Fiesta'];
     */
    public function generateMemberProfile(string $username, int $age, array $interests): array
    {
        if (empty($username)) {
            throw new InvalidArgumentException("Le nom d'utilisateur ne peut pas être vide.");
        }

        if ($age < 18) {
            throw new InvalidArgumentException("Le membre doit être majeur.");
        }

        return [
            'id' => uniqid('usr_'),
            'meta' => [
                'username' => $username,
                'clean_name' => strtolower(trim($username)),
                'age' => $age,
            ],
            'preferences' => [
                'interests' => array_map('strtolower', $interests),
                'count' => count($interests)
            ],
            'status' => 'active',
            'created_at' => date('Y-m-d')
        ];
    }

    /**
     * Calcule une réduction basée sur un code promo et un montant.
     * Specifications :
     * - Le code promo "VIP20" applique une réduction de 20%
     * - Le code promo "SUMMER50" applique une réduction de 50%
     * - Si le code promo est null ou invalide, aucun rabais n'est appliqué et le montant original est retourné
     * - Tout autre code promo sauf null doit throw une InvalidArgumentException (héhé attention cette specification n'est pas implémentée dans la méthode)
     */
    public function applyPromoCode(float $amount, string|null $code): float
    {
        if (!$code) {
            return $amount;
        }
        $code = strtoupper($code);

        if ($code === 'VIP20') {
            return $amount * 0.80;
        }

        if ($code === 'SUMMER50') {
            return $amount * 0.50;
        }

        throw new InvalidArgumentException("Codigo invalido");
    }

    /**
     * Vérifie si un membre est éligible à une mise à niveau premium.
     * - Le membre doit avoir au moins 3 centres d'intérêt
     * - Le membre doit être âgé d'au moins 18 ans
     * - Le montant dépensé doit être supérieur ou égal à 100
     */
    public function isEligibleForUpgrade(int $age, array $interests, float $totalSpent): bool
    {
        if ($age < 18) {
            return false;
        }

        if (count($interests) < 3) {
            return false;
        }

        if ($totalSpent < 100) {
            return false;
        }

        return true;
    }

    /**
     * Calcule les points de fidélité d'un membre en fonction de ses achats.
     * - Chaque euro dépensé rapporte 10 points
     * - Les membres premium obtiennent un bonus de 50% sur leurs points
     * - Le montant doit être positif
     */
    public function calculateLoyaltyPoints(float $amount, bool $isPremium = false): int
    {
        if ($amount < 0) {
            throw new InvalidArgumentException("Le montant ne peut pas être négatif.");
        }

        $points = (int) ($amount * 10);

        if ($isPremium) {
            $points = (int) ($points * 1.5);
        }

        return $points;
    }

    /**
     * Génère un résumé des dépenses d'un membre.
     * - Le tableau de transactions ne peut pas être vide
     * - Retourne un tableau avec le total, la moyenne, le minimum et le maximum des dépenses
     */
    public function summarizeSpending(array $transactions): array
    {
        if (empty($transactions)) {
            throw new InvalidArgumentException("Le tableau de transactions ne peut pas être vide.");
        }

        return [
            'total'   => array_sum($transactions),
            'average' => array_sum($transactions) / count($transactions),
            'min'     => min($transactions),
            'max'     => max($transactions),
        ];
    }

    /**
     * Renouvelle l'abonnement premium d'un membre.
     * - La durée doit être de 1, 6 ou 12 mois
     * - Retourne la nouvelle date d'expiration au format Y-m-d
     */
    public function renewSubscription(int $months): string
    {
        if (!in_array($months, [1, 6, 12])) {
            throw new InvalidArgumentException("La durée doit être de 1, 6 ou 12 mois.");
        }

        return date('Y-m-d', strtotime("+{$months} months"));
    }

    /**
     * Anonymise le profil d'un membre.
     * - Le profil doit contenir les champs id, meta et preferences
     * - Le nom d'utilisateur est remplacé par "anonymous"
     * - Les centres d'intérêt sont vidés
     * - L'âge est remplacé par 0
     */
    public function anonymizeProfile(array $profile): array
    {
        if (!isset($profile['id'], $profile['meta'], $profile['preferences'])) {
            throw new InvalidArgumentException("Le profil est invalide ou incomplet.");
        }

        $profile['meta']['username']   = 'anonymous';
        $profile['meta']['clean_name'] = 'anonymous';
        $profile['meta']['age']        = 0;
        $profile['preferences']['interests'] = [];
        $profile['preferences']['count']     = 0;

        return $profile;
    }
}
