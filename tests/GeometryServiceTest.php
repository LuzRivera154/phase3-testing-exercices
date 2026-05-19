<?php

namespace App\Tests;

use App\Services\GeometryService;
use Override;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Activité 1 : Testez la classe GeometryService
 * Cette exercice est simple et a pour but de vous familiariser avec l'écriture de tests unitaires pour des méthodes de calcul.
 * La classe GeometryService contient plusieurs méthodes qui calculent des aires et des volumes pour différentes formes géométriques.
 * Votre tâche est d'écrire des tests unitaires pour chacune de ces méthodes
 */
class GeometryServiceTest extends KernelTestCase
{
    private GeometryService $geoService;

    #[Override]
    protected function setUp(): void
    {
        self::bootKernel();
        $this->geoService = static::getContainer()->get(GeometryService::class);
    }
    public function testCalculateSquareArea(): void
    {

        $squareArea = $this->geoService->calculateSquareArea(5);
        $this->assertEquals(25, $squareArea, "La surface d'un carré de coté 5 doit être égal à 25");
    }

    // Remplissez les test restants :)

    public function testCalculateCircleArea(): void
    {
        #assertEqualsWithDelta = Con un margen de error (Cuando es numero largo)
        $circleArea = $this->geoService->calculateCircleArea(3);
        $this->assertEqualsWithDelta(28.27, $circleArea, 0.01, "Esta bien, se permite un margen de error de 0.01");
    }
    public function testCalculateRectangleArea(): void
    {
        $rectangleArea = $this->geoService->calculateRectangleArea(10, 10);
        $this->assertEquals(100, $rectangleArea,"El resultado es 100");
    }
    public function testCalculateTriangleArea(): void
    {
        $triangleArea = $this->geoService->calculateTriangleArea(7,7);
        $this->assertEquals(24.5, $triangleArea, "Esta bien el resultado es 24,05");
    }
    public function testCalculateCubeVolume(): void
    {
        $cubeVolume = $this->geoService->calculateCubeVolume(3);
        $this->assertEquals(27, $cubeVolume, "El resultado tiene que ser 27");

    }
    public function testCalculateCylinderVolume(): void
    {
       $cylinderVolume = $this->geoService->calculateCylinderVolume(4,2);
       $this->assertEqualsWithDelta(100.52, $cylinderVolume, 0.02, "Margen de error de 0.2 dependiendo de como se calcule el num pi");
    }
    public function testCalculateConeVolume(): void
    {
       $coneVolume = $this->geoService->calculateConeVolume(2,2);
       $this->assertEqualsWithDelta(8.38, $coneVolume, 0.01);
    }
}
