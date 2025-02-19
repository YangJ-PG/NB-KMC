<?php
 /*
     * 包装类型的输出
     * User: yangJian
     * Date: 2025/02/18
     * Time: 13：40
  */
class Main
{
    public function test(float $length, float $width, float $height, float $weight): array
    {
        // 数值转换(长度和重量转换时需要向上取整)
        // 1 in（英寸）= 2.54 cm
        $length_in      =   ceil($length / 2.54);
        $width_in       =   ceil($width / 2.54);
        $height_in      =   ceil($height / 2.54);
        // 1 LB（磅）= 0.454 kg
        $weight_lb      =   ceil($weight / 0.454);

        // 围长 = 最长边 + (次长边 + 第三边) * 2 （单位 in）
        $girths         =   [$length_in, $width_in, $height_in];
        rsort($girths);
        $girth          =   $girths[0] + ($girths[1] + $girths[2]) * 2;

        // 体积重 = 最长边 * 次长边 * 第三边 / 体积重基数 （结果向上取整）
        // 体积重基数：250
        $weight_vol     =   ceil(($girths[0] * $girths[1] * $girths[2]) / 250);

        // 实重 = 产品重量（LB）和体积重之间取最大值
        $weight_net     =   max($weight_lb, $weight_vol);

        // 类型输出
        $types          =   [];

        // OUT_SPACE:（实重大于150）或（最长边大于108）或（围长大于165）
        //当满足 OUT_SPACE 类型，不再判断 OVERSIZE 或 AHS
        if ($weight_net > 150 || $girths[0] > 108 || $girth > 165) {
            $types[]    =   'OUT_SPACE';
            return $types;
        }

        // OVERSIZE:（围长大于130，小于等于165）或（最长边大于等于96小于108）
        //当满足 OVERSIZE，不再判断 AHS
        if (($girth > 130 && $girth <= 165) || ($girths[0] >= 96 && $girths[0] <= 108)) {
            $types[]    =   'OVERSIZE';
            return $types;
        }

        // AHS-WEIGHT:实重大于50，小于等于150
        if($weight_net > 50 && $weight_net <= 150){
            $ahs_weight =   true;
        }
        // AHS-SIZE: （围长大于105）或（最长边大于等于48，最长边小于108）或（次长边大于等于30）
        if(($girth > 105) || ($girths[0] >= 48 && $girths[0] < 108) || ($girths[1] >= 30)){
            $ahs_size   =   true;
        }

        if (isset($ahs_weight) && isset($ahs_size)) {
            $types[]    =   'AHS-WEIGHT';
            $types[]    =   'AHS-SIZE';
        } elseif (isset($ahs_weight)) {
            $types[]    =   'AHS-WEIGHT';
        } elseif (isset($ahs_size)) {
            $types[]    =   'AHS-SIZE';
        }

        return $types;
    }
}

$obj                    =   new Main();
var_dump($obj->test(68, 70, 60, 23));
echo '<br/>';
var_dump($obj->test(114.50, 42, 26, 47.5));
echo '<br/>';
var_dump($obj->test(162, 60, 11, 14));
echo '<br/>';
var_dump($obj->test(113, 64, 42.5, 35.85));
echo '<br/>';
var_dump($obj->test(114.5, 17, 51.5, 16.5));
echo '<br/>';


/*
 * 输出：
array(2) { [0]=> string(10) "AHS-WEIGHT" [1]=> string(8) "AHS-SIZE" }
array(1) { [0]=> string(10) "AHS-WEIGHT" }
array(1) { [0]=> string(8) "AHS-SIZE" }
array(1) { [0]=> string(8) "OVERSIZE" }
array(0) { }

*/
