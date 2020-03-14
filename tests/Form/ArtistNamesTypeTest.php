<?php


namespace App\Tests\Form;


use App\Form\ArtistNamesType;
use Symfony\Component\Form\Test\TypeTestCase;

class ArtistNamesTypeTest extends TypeTestCase
{
    public function test()
    {
        $form = $this->factory->create(ArtistNamesType::class);
        $form->submit([
            'artistName1' => '雨のパレード',
            'artistName2' => 'LILILIMIT'
        ]);
        $this->assertTrue($form->isValid());
    }
}
