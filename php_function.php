/**
 * 将科学计数法的数字转换为正常的数字
 * 为了将数字处理完美一些，使用部分正则是可以接受的
 * @author loveyu
 * @param string $number
 * @return string
 */
function convert_scientific_number_to_normal($number)
{
    if(stripos($number, 'e') === false) {
        //判断是否为科学计数法
        return $number;
    }

    if(!preg_match(
        "/^([\\d.]+)[eE]([\\d\\-\\+]+)$/",
        str_replace(array(" ", ","), "", trim($number)), $matches)
    ) {
        //提取科学计数法中有效的数据，无法处理则直接返回
        return $number;
    }

    //对数字前后的0和点进行处理，防止数据干扰，实际上正确的科学计数法没有这个问题
    $data = preg_replace(array("/^[0]+/"), "", rtrim($matches[1], "0."));
    $length = (int)$matches[2];
    if($data[0] == ".") {
        //由于最前面的0可能被替换掉了，这里是小数要将0补齐
        $data = "0{$data}";
    }

    //这里有一种特殊可能，无需处理
    if($length == 0) {
        return $data;
    }

    //记住当前小数点的位置，用于判断左右移动
    $dot_position = strpos($data, ".");
    if($dot_position === false) {
        $dot_position = strlen($data);
    }

    //正式数据处理中，是不需要点号的，最后输出时会添加上去
    $data = str_replace(".", "", $data);


    if($length > 0) {
        //如果科学计数长度大于0

        //获取要添加0的个数，并在数据后面补充
        $repeat_length = $length - (strlen($data) - $dot_position);
        if($repeat_length > 0) {
            $data .= str_repeat('0', $repeat_length);
        }

        //小数点向后移n位
        $dot_position += $length;
        $data = ltrim(substr($data, 0, $dot_position), "0").".".substr($data, $dot_position);

    } elseif($length < 0) {
        //当前是一个负数

        //获取要重复的0的个数
        $repeat_length = abs($length) - $dot_position;
        if($repeat_length > 0) {
            //这里的值可能是小于0的数，由于小数点过长
            $data = str_repeat('0', $repeat_length).$data;
        }

        $dot_position += $length;//此处length为负数，直接操作
        if($dot_position < 1) {
            //补充数据处理，如果当前位置小于0则表示无需处理，直接补小数点即可
            $data = ".{$data}";
        } else {
            $data = substr($data, 0, $dot_position).".".substr($data, $dot_position);
        }
    }
    if($data[0] == ".") {
        //数据补0
        $data = "0{$data}";
    }
    return trim($data, ".");
}
