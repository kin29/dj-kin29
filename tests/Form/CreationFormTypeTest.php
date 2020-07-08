<?php

namespace App\Tests\Form;

use App\Form\CreationFormType;
use Symfony\Component\Form\Test\TypeTestCase;

class CreationFormTypeTest extends TypeTestCase
{
    public function test()
    {
        $form = $this->factory->create(CreationFormType::class);
        $this->assertStringContainsString('dj-kin29-', $form->get('playlistName')->getData());

        $form->submit([
            'artistNames' => [
                'artistName1' => '雨のパレード',
                'artistName2' => 'TENDRE',
                'artistName3' => 'KOTORI',
                'artistName4' => 'LILILIMIT',
                'artistName5' => '我儘ラキア',
            ],
            'playlistName' => 'わたしのプレイリスト',
            'isPrivate' => true,
        ]);
        $this->assertTrue($form->isValid());
        $this->assertEquals('雨のパレード', $form->get('artistNames')->get('artistName1')->getData());
        $this->assertEquals('TENDRE', $form->get('artistNames')->get('artistName2')->getData());
        $this->assertEquals('KOTORI', $form->get('artistNames')->get('artistName3')->getData());
        $this->assertEquals('LILILIMIT', $form->get('artistNames')->get('artistName4')->getData());
        $this->assertEquals('我儘ラキア', $form->get('artistNames')->get('artistName5')->getData());
        $this->assertEquals('わたしのプレイリスト', $form->get('playlistName')->getData());
        $this->assertTrue($form->get('isPrivate')->getData());
    }
}
