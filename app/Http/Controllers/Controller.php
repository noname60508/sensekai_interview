<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public $defaultPageSize = 10;

    public function ISBN10Check($ISBN): bool
    {
        $ISBN = str_replace('-', '', $ISBN); // ハイフンを除去
        $sum = 0;

        if (strlen($ISBN) != 10) {
            return false; // 長さが10でない場合は無効
        }

        for ($i = 0; $i < 9; $i++) {
            if (!is_numeric($ISBN[$i])) {
                return false; // 数字以外が含まれている場合は無効
            }
            $sum += (int)$ISBN[$i] * (10 - $i);
        }
        $checkDigit = $ISBN[9] == 'X' ? 10 : (int)$ISBN[9];
        $sum += $checkDigit;
        return ($sum % 11 == 0);
    }

    public function ISBN13Check($ISBN): bool
    {
        $ISBN = str_replace('-', '', $ISBN); // ハイフンを除去
        $sum = 0;

        if (strlen($ISBN) != 13) {
            return false; // 長さが13でない場合は無効
        }

        for ($i = 0; $i < 12; $i++) {
            if (!is_numeric($ISBN[$i])) {
                return false; // 数字以外が含まれている場合は無効
            }
            $sum += (int)$ISBN[$i] * ($i % 2 == 0 ? 1 : 3);
        }
        $checkDigit = (10 - ($sum % 10)) % 10;
        return ($checkDigit == (int)$ISBN[12]);
    }
}
