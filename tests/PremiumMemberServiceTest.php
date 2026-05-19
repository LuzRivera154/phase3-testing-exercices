<?php

namespace App\Tests;

use App\Services\PremiumMemberService;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


/**
 * Activité 2 : Testez la classe PremiumMemberService
 * Doc des asserts de PHPUnit : https://docs.phpunit.de/en/13.1/assertions.html
 * Cette exercice est un peu plus dur et plus realiste.
 * Il s'agit de tester la classe PremiumMemberService qui contient des méthodes plus complexes que celles de GeometryService.
 * - La méthode generateMemberProfile génère un profil de membre à partir de son nom d'utilisateur, son âge et ses centres d'intérêt. Elle doit respecter plusieurs specifications que vous trouverez dans les commentaires de la méthode.
 * - La méthode applyPromoCode applique une réduction à un montant en fonction d'un code promo. Elle doit respecter plusieurs specifications que vous trouverez dans les commentaires de la méthode.
 * CERTAIN specification non pas été respectées dans l'implémentation de la classe PremiumMemberService, votre travail est de les identifier et de les tester correctement.
 * CERTAIN Test devrons donc échoué et c'est le but c'est la preuve que votre test et bien ecrit car il respecte la spec et pas juste l'implémentation.
 * C'est ce cette façon qu'on l'on évite d'écrire des test biasé.
 */
class PremiumMemberServiceTest extends KernelTestCase
{
    private PremiumMemberService $premiumMemberService;
    protected function setUp(): void
    {
        // Plutot que de faire new PremiumMemberService() on va le récuperer depuis le container de symfony pour être sur d'avoir la même instance 
        // que celle utilisée dans l'application c'est obligatoire pour des services plus réaliste qui inject des Repo ou d'autre Service par exemple.

        self::bootKernel();
        $this->premiumMemberService = static::getContainer()->get(PremiumMemberService::class);
    }
    // Remplissez les test restants :)
    // Bon courage héhé :)

    /**
     * Test la fonction generateMemberProfile pour un cas de SUCCES.
     * - assertIsArray
     * - assertArrayHasKey
     * - assertStringStartsWith
     * - assertSame : pour comparer deux tableaux associatifs
     * - assertMatchesRegularExpression
     * - Voir la doc pour les autres asserts : https://docs.phpunit.de/en/13.1/assertions.html
     */

    public function testGenerateMemberProfileSuccess(): void
    {
        $result = $this->premiumMemberService->generateMemberProfile("Pepito", 25, ['Coding', 'Gaming']);


        $this->assertArrayHasKey('meta', $result);
        $this->assertArrayHasKey('username', $result['meta']);
        $this->assertArrayHasKey('clean_name', $result['meta']);
        $this->assertArrayHasKey('age', $result['meta']);

        $this->assertArrayHasKey('preferences', $result);
        $this->assertArrayHasKey('interests', $result['preferences']);
        $this->assertArrayHasKey('count', $result['preferences']);

        $this->assertStringStartsWith('usr_', $result['id']);
        $this->assertGreaterThanOrEqual('18', $result['meta']['age']);
        $this->assertEquals('active', $result['status']);
        $this->assertMatchesRegularExpression('/^[a-z]+$/', $result['meta']['clean_name']);

        $interests = $result['preferences']['interests'];
        foreach ($interests as $interest) {
            $this->assertMatchesRegularExpression('/^[a-z]+$/', $interest);
        }
        $date = date('Y-m-d');
        $this->assertEquals($date, $result['created_at']);
    }

    /**
     * Test la fonction generateMemberProfile pour un cas d'ECHEC lorsque le nom d'utilisateur est vide.
     */


    public function testGenerateMemberProfileEmptyUsername(): void
    {
        // ExpectExeception prepart la levé d'exeption, pour les exeptions on utilise 
        // expect plutot que assert
        // Utilisez cette exemple pour tester les autres expections dans d'autre test.
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Le nom d'utilisateur ne peut pas être vide.");
        $this->premiumMemberService->generateMemberProfile("", 25, ['Coding', 'Gaming']);
    }

    public function testGenerateMemberProfileThrowsExceptionForUnderage(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Le membre doit être majeur.");
        $this->premiumMemberService->generateMemberProfile("pepito", 5, ['comer', 'Jugar']);
    }

    public function testGenerateMemberProfileThrowsExceptionForEmptyUsername(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Le nom d'utilisateur ne peut pas être vide.");
        $this->premiumMemberService->generateMemberProfile("", 19, ['comer', 'Jugar']);
    }

    public function testApplyPromoCodeVip(): void
    {
        $codeVip = $this->premiumMemberService->applyPromoCode(100, 'VIP20');
        $this->assertEquals(80, $codeVip);
    }


    public function testIsEligibleForUpgrade(): void
    {
        $premium = $this->premiumMemberService->isEligibleForUpgrade(25, ['correr', 'jugar', 'cantar'], 150);
        $this->assertTrue($premium);
    }


    public function testApplyPromoCodeSummer50(): void
    {

        $codeSummer = $this->premiumMemberService->applyPromoCode(100, 'SUMMER50');
        $this->assertEquals(50, $codeSummer);
    }

    public function testApplyPromoCodeThrowExceptionInvalid(): void
    {

        $this->expectException(InvalidArgumentException::class);
        $this->premiumMemberService->applyPromoCode(100, "grr");
    }

    public function testApplyPromoCodeNullAmountUnchanged(): void
    {
        $codeFalso = $this->premiumMemberService->applyPromoCode(100, null);
        $this->assertEquals(100, $codeFalso);
    }

    public function testIsEligibleForUpgradeSuccess(): void
    {
        $premium = $this->premiumMemberService->isEligibleForUpgrade(25, ['correr', 'jugar', 'cantar'], 150);
        $this->assertTrue($premium);
    }

    public function testIsEligibleForUpgradeUnderAge(): void
    {
        $premium = $this->premiumMemberService->isEligibleForUpgrade(15, ['correr', 'jugar', 'cantar'], 150);
        $this->assertFalse($premium);
    }

    // C'est encore loin ? 8( 

    public function testIsEligibleForUpgradeInsufficientInterests(): void
    {
        $premium = $this->premiumMemberService->isEligibleForUpgrade(19, ['jugar', 'cantar'], 150);
        $this->assertFalse($premium);
    }

    public function testIsEligibleForUpgradeInsufficientSpent(): void
    {
        $premium = $this->premiumMemberService->isEligibleForUpgrade(19, ['jugar', 'cantar', 'bailar'], 20);
        $this->assertFalse($premium);
    }


    public function testCalculateLoyaltyPointsStandard(): void
    {
        $loyalty = $this->premiumMemberService->calculateLoyaltyPoints(5, false);
        $this->assertEquals(50, $loyalty);
    }

    public function testCalculateLoyaltyPointsPremium(): void
    {
        $loyalty = $this->premiumMemberService->calculateLoyaltyPoints(5, true);
        $this->assertEquals(75, $loyalty);
    }

    public function testCalculateLoyaltyPointsNegativeThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->premiumMemberService->calculateLoyaltyPoints(-1, false);
    }

    public function testSummarizeSpending(): void
    {
        $spending = $this->premiumMemberService->summarizeSpending([1, 2, 5, 6]);
        $this->assertIsArray($spending);
        $this->assertEquals(14, $spending['total']);
        $this->assertEquals(3.5, $spending['average']);
        $this->assertEquals(1, $spending['min']);
        $this->assertEquals(6, $spending['max']);
    }

    public function testSummarizeSpendingEmptyThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->premiumMemberService->summarizeSpending([]);
    }

    // On a presque fini :)

    public function testRenewSubscription1Month(): void
    {
        $sub = $this->premiumMemberService->renewSubscription(1);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $sub);
    }

    public function testRenewSubscriptionInvalidDurationThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->premiumMemberService->renewSubscription(5);
    }

    public function testAnonymizeProfile(): void
    {
        $profile = $this->premiumMemberService->generateMemberProfile("Pepito", 25, ['Coding', 'Gaming']);
        $profileAnon = $this->premiumMemberService->anonymizeProfile($profile);
        $this->assertArrayHasKey('username', $profileAnon['meta']);
        $this->assertContains('anonymous', $profileAnon['meta']);
        $this->assertEquals(0, $profileAnon['meta']['age']);
        $this->assertEquals(0, $profileAnon['preferences']['count']);
        $this->assertIsArray($profileAnon);
        $this->assertContainsOnlyNull($profileAnon['preferences']['interests']);
    }

    public function testAnonymizeProfileInvalidThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->premiumMemberService->anonymizeProfile(["Pepito", 23, ""]);
    }
}
