<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class Converter extends ServiceProvider {

    /**
     * Register services.
     */
    public function register(): void {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void {
        //
    }

    /**
     * Converter o valor Real para float ex: R$ 3.125,55
     * 
     * @static
     * @access public
     * @param float $value
     * @return string
     */
    public static function realFloat($value) {
        $v = str_replace("R$", "", $value);
        $v = trim($v);
        $v = str_replace(".", "", $v);
        $v = str_replace(",", ".", $v);
        return $v;
    }

    /**
     * Converter uma porcentagem para float ex: 15,25%
     * 
     * @static
     * @access public
     * @param float $value
     * @return string
     */
    public static function porcentagemFloat($value) {
        $v = str_replace("%", "", $value);
        $v = trim($v);
        $v = str_replace(",", ".", $v);
        return $v;
    }

    /**
     * Converter float para Real ex: 20,00
     * 
     * @static
     * @access public
     * @param float $value
     * @return string
     */
    public static function floatReal($value, $format = 2) {
        return number_format($value, $format, ",", ".");
    }

}
