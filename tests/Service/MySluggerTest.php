<?php

namespace App\Tests\Service;

use App\Service\MySlugger;
use PHPUnit\Framework\TestCase;
use Symfony\Component\String\Slugger\AsciiSlugger;

class MySluggerTest extends TestCase
{
    /**
    * @dataProvider slugProvider
    */
   public function testSlugify($title, $expected, $isLower): void
   {
        $slugger = new MySlugger(new AsciiSlugger(), $isLower);
        $this->assertEquals($expected, $slugger->slugTitle($title));
    }

    // REFER : https://docs.phpunit.de/en/9.6/writing-tests-for-phpunit.html#data-providers
   public static function slugProvider(): Array
   {
           // on crée un tableau de tests avec l'élément envoyé et l'élément attendu et le isLower
           return [
            ['Jo le Taxi', 'Jo-le-Taxi', false],
            ['Bonjour ça va bien', 'Bonjour-ca-va-bien', false],
            ['avec nombre 42 ?', 'avec-nombre-42', false],
            ['Jo le Taxi', 'jo-le-taxi', true],
            ['Bonjour ça va bien', 'bonjour-ca-va-bien', true],
            ['avec nombre 42 ?', 'avec-nombre-42', true],
        ];
   }
}
