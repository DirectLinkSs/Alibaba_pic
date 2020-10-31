<?php
/**
 * 阿里图床上传
 * @author: 阿珏 (QQ群：712473912)
 * @link: http://www.52ecy.cn
 * @version: 1.1
 */
$file = $_FILES['file'];
if (is_uploaded_file($file['tmp_name'])) {
    $arr = pathinfo($file['name']);
    $ext_suffix = $arr['extension'];
    $allow_suffix = array('jpg', 'gif', 'jpeg', 'png');
    if (!in_array($ext_suffix, $allow_suffix)) {
        msg(['code' => 1, 'msg' => '上传格式不支持']);
    }
    $new_filename = time() . rand(100, 1000) . '.' . $ext_suffix;
    if (move_uploaded_file($file['tmp_name'], $new_filename)) {
        $data = upload('https://support.qq.com/api/v1/284818/posts/upload/images', $new_filename);
        $pattern = json_decode($data);
        @unlink($new_filename);
        if ($pattern->status == 0) {
            msg(['code' => 0, 'msg' => $pattern->data->image_url]);
        } else {
            msg(['code' => 1, 'msg' => '上传失败']);
        }
    } else {
        msg(['code' => 1, 'msg' => '上传数据有误']);
    }
} else {
    msg(['code' => 1, 'msg' => '上传数据有误']);
}
function upload($url, $file)
{
    return get_url($url, ['type' => 'post', 'name' => $file, 'upload' => new \CURLFile(realpath($file))]);
}
function get_url($url, $post)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Referer:https://support.qq.com/embed/phone/284818/new-post?']);
    curl_setopt($ch, CURLOPT_COOKIE, '_tucao_session=MmxUbGRkT2tIanhiMEM0MTFmcDFmSk8rdi9Cd0JHeldvV1lVV0RPRnJRWDUyWGw2UUU5OHBoNy92K0tsalZsaFVzeFd1YW12TFVJOXNtQ0szOGNrRDFERXJUUUlEeUdoZVBSQUlFd25US2M9--6VN4INDoLvrGXoNwTaKIog%3D%3D');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if ($post) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }
    if (curl_exec($ch) === false) {
        echo 'Curl error: ' . curl_error($ch);
    }
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
function msg($data)
{
    exit(json_encode($data));
}
