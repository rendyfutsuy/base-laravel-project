<?php

namespace Tests\Unit;

use Tests\TestCase;

class HelperTest extends TestCase
{
    /*
    |--------------------------------------------------------------------------
    | Helpers Unit Test
    |--------------------------------------------------------------------------
    |
    | File Path: app\Http\helpers.php
    | Goal: Assert all helpers work the way its should be.
    |
    */

    /** @test */
    public function number_abbr_can_abbreviate_large_numbers_correctly()
    {
        $this->assertEquals('15', number_abbr(15));
        $this->assertEquals('129', number_abbr(129));
        $this->assertEquals('400', number_abbr(400));
        $this->assertEquals('1,5rb', number_abbr(1500));
        $this->assertEquals('14,4rb', number_abbr(14350));
        $this->assertEquals('30,5rb', number_abbr(30489));
        $this->assertEquals('50,2rb', number_abbr(50222));
        $this->assertEquals('104rb', number_abbr(103977));
        $this->assertEquals('2,5jt', number_abbr(2540388));
        $this->assertEquals('53jt', number_abbr(53003839));
    }

    /** @test */
    public function price_abbr_can_abbreviate_large_numbers_on_price_correctly()
    {
        $this->assertEquals('15', price_abbr(15));
        $this->assertEquals('129', price_abbr(129));
        $this->assertEquals('400', price_abbr(400));
        $this->assertEquals('1,500', price_abbr(1500));
        $this->assertEquals('14,350', price_abbr(14350));
        $this->assertEquals('30,489', price_abbr(30489));
        $this->assertEquals('50,222', price_abbr(50222));
        $this->assertEquals('103,977', price_abbr(103977));
        $this->assertEquals('2,540,388', price_abbr(2540388));
        $this->assertEquals('53,003,839', price_abbr(53003839));
    }

    /** @test */
    public function price_formatted_can_generate_number_based_on_currency_that_submitted()
    {
        $this->assertEquals('Rp.129', price_formatted(129));
        $this->assertEquals('Rp.1.500', price_formatted(1500));
        $this->assertEquals('Rp.400.000', price_formatted(400000));
        $this->assertEquals('Rp.4.000.000', price_formatted(4000000));

        $this->assertEquals('$ 129', price_formatted(129, '$ '));
        $this->assertEquals('$ 1.500', price_formatted(1500, '$ '));
        $this->assertEquals('$ 400.000', price_formatted(400000, '$ '));
        $this->assertEquals('$ 4.000.000', price_formatted(4000000, '$ '));
    }

    /** @test */
    public function mask_mail_can_hide_some_part_of_your_email()
    {
        $this->assertEquals('ren************@gma******', mask_email('rendy.anggara@gmail.com'));
    }

    /** @test */
    public function word_counter_can_count_word_by_word()
    {
        $this->assertEquals(1, word_counter('rendy'));
        $this->assertEquals(3, word_counter('rendy anggara ganteng'));
        $this->assertEquals(3, word_counter('rendy-anggara-ganteng'));
        $this->assertEquals(3, word_counter('rendy_anggara_ganteng'));
    }

    /** @test */
    public function test_title_helpers()
    {
        $this->assertEquals('Rendy', title('rendy'));
        $this->assertEquals('RendyAnggara', title('rendy_anggara'));
        $this->assertEquals('RendyAnggara', title('rendy anggara'));
        $this->assertEquals('Rendyanggara', title('rendyanggara'));
    }

    /** @test */
    public function test_phone_generator_helpers()
    {
        $phone = '08123456789';

        $arrayPhone = phone($phone);

        $this->assertEquals('08123456789', $arrayPhone['international']);
        $this->assertEquals('628123456789', $arrayPhone['local']);
        $this->assertEquals('8123456789', $arrayPhone['without_code']);

        $phone = '628123456789';

        $arrayPhone = phone($phone);

        $this->assertEquals('08123456789', $arrayPhone['international']);
        $this->assertEquals('628123456789', $arrayPhone['local']);
        $this->assertEquals('8123456789', $arrayPhone['without_code']);

        $phone = '+628123456789';

        $arrayPhone = phone($phone);

        $this->assertEquals('08123456789', $arrayPhone['international']);
        $this->assertEquals('628123456789', $arrayPhone['local']);
        $this->assertEquals('8123456789', $arrayPhone['without_code']);

        $phone = '8123456789';

        $arrayPhone = phone($phone);

        $this->assertEquals('08123456789', $arrayPhone['international']);
        $this->assertEquals('628123456789', $arrayPhone['local']);
        $this->assertEquals('8123456789', $arrayPhone['without_code']);
    }
}
