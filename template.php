<?php
// $Id$

/**
 * @Implement of hook_settings_form()
 */
function rover_settings_form(&$form, $conf) {
  
  $form['fields']['settings']['contact'] = array(
    '#fieldset_prefix' => 'asc',
    '#fieldset_suffix' => 1,
    '#fieldset_legend' => t('rover', '联系信息'),
  );

  $form['fields']['settings']['contact']['address'] = array(
    '#title' => t('rover', '地址'),
    '#type' => 'textfield',
    '#default_value' => $conf['settings']['contact']['address']
  );

  $form['fields']['settings']['contact']['phone'] = array(
    '#title' => t('rover', '电话'),
    '#type' => 'textfield',
    '#default_value' => $conf['settings']['contact']['phone']
  );

  $form['fields']['settings']['contact']['mobile'] = array(
    '#title' => t('rover', '手机'),
    '#type' => 'textfield',
    '#default_value' => $conf['settings']['contact']['mobile']
  );

  $form['fields']['settings']['contact']['mail'] = array(
    '#title' => t('rover', '邮箱'),
    '#type' => 'textfield',
    '#default_value' => $conf['settings']['contact']['mail']
  );

  $form['fields']['settings']['contact']['qq'] = array(
    '#title' => t('rover', 'QQ'),
    '#type' => 'textfield',
    '#default_value' => $conf['settings']['contact']['qq']
  );

  $form['fields']['settings']['is_content_top'] = array(
    '#title' => t('rover', '顶部幻灯片'),
    '#default_value' => $conf['settings']['is_content_top'],
    '#type' => 'radio',
    '#description' => t('rover', '全站启用不包括管理页'),
    '#options' => array(t('rover', '不启用'), t('rover', '仅首页启用'), t('rover', '全站启用'))
  );
 
  // 幻灯片记录保存到 custom 表中
  $data = custom_get('rover_themes_content_top');
  for ($i = 0; $i < 5; $i++) {
    $rows = !empty($data[$i]) ? $data[$i] : array();
    $form['fields']['content_top_' . $i] = array();
    $form['fields']['content_top_' . $i]['title'] = array(
      '#title' => t('rover', '标题'),
      '#type' => 'textfield',
      '#fieldset_prefix' => 'asc',
      '#fieldset_legend' => t('rover', '幻灯片 %length 设置', array('%length' => $i)),
      '#default_value' => $rows['title'],
    );

    $form['fields']['content_top_' . $i]['link'] = array(
      '#title' => t('rover', '链接'),
      '#default_value' => $rows['link'],
      '#type' => 'textfield',
    );

    $form['fields']['content_top_' . $i . '_file'] = array(
      '#title' => t('rover', '文件'),
      '#description' => !empty($rows['file']) ? l(t('rover', '当前图片'), $rows['file'], array('file' => 1, 'attributes' => array('target' => '_blank'))) : '',
      '#attributes' => array('name' => 'content_top_' . $i),
      '#fieldset_suffix' => 1,
      '#type' => 'file',
    );
  }
  
  $form['fields']['content_top_size'] = array(
    '#title' => t('rover', '图片尺寸'),
    '#default_value' => !empty($data['size']) ? $data['size'] : '950x310',
    '#description' => t('rover', '将自动缩放至设定的尺寸，如：950x310，宽度不宜大于 950'),
    '#type' => 'textfield' 
  );

  $form['settings']['#validate'][] = '_rover_themes_setting_validate';
}

function _rover_themes_setting_validate(&$form, &$v) {
  $data = array();
  $old = custom_get('rover_themes_content_top');
  for ($i = 0; $i < 5; $i++) {
    $rows = $v['content_top_' . $i];
    if (!$error = file_validate_error($_FILES['content_top_' . $i . '_file']['error'])) {
      if ($file = file_save_upload($_FILES['content_top_' . $i . '_file'],
      array('image' => true, 'savepath' => 'cache/themes', 'no_insert' => 1, 'filename' => 'rover_content_top_' . $i), 0)) {
        // 删除旧文件
        if (!empty($old[$i]) && !empty($old[$i]['file']) && $old[$i]['file'] != $file->filepath) {
          file_delete_file($old[$i]['file'], 'themes_rover_' . $i);
        } else {
          // 删除缩略图
          file_delete_dir(dirname($file->filepath) . '/themes_rover_' . $i);
        }
        $rows['file'] = $file->filepath;
      } else {
        dd_set_message($rows['title'] . '失败');
        continue;
      }
    } else if (!empty($old[$i]) && !empty($old[$i]['file'])) {
      $rows['file'] = $old[$i]['file'];
    } else {
      continue;
    }
    $data[$i] = $rows;
  }

  if (!empty($data)) {
    $data['size'] = $v['content_top_size'];
    custom_set('rover_themes_content_top', $data);
  } else {
    custom_set('rover_themes_content_top', array());
  }
}

/**
 * 获取内容顶部区域
 */
function _rover_get_content_top() {
  global $conf, $is_admin; 
  
  $is_show = $conf['themes']['rover']['settings']['is_content_top'];

  if (empty($is_admin) && ($is_show == 2 || ($is_show == 1 && dd_is_front())) && $data = custom_get('rover_themes_content_top')) {
    $html = '';
    for ($i = 0; $i < 5; $i++) {
      if (!empty($data[$i])) {
        $rows = $data[$i];
      } else {
        continue;
      }
      $html .= '<li thumb="' . f(image_get_thumb('themes_rover_' . $i, $rows['file'], '70x40', 'scale_and_crop')) . '">';
      if (!empty($rows['link'])) {
        $html .= '<a href="' . $rows['link'] . '" title="' . $rows['title'] . '"';
        if ($rows['target']) {
          $html .= ' target="_blank"';
        }
        $html .= '>';
      }
      $html .= '<img src="' . f(image_get_thumb('themes_rover_' . $i, $rows['file'], $data['size'])) . '" alt="' . $rows['title'] . '" />';
      $html .= '</a>';
      $html .= '</li>';
    }

    if (!empty($html)) {
      list($width, $height) = explode('x', $data['size']);
      $output = '<div id="content_top"><div class="content_focus_image" id="content_focus_slider">';
      $output .= '<ul class="focus_change_list">' . $html . '</ul>';
      $output .= '</div></div>';
      $output .= '<style>';
      $output .= '.focus_change_list img {max-height: ' . $height . 'px;}';
      $output .= '.focus_change_list {height: ' . $height . 'px; line-height: ' . $height . 'px;}';
      $output .= '.content_focus_image {height: ' . $height . 'px; width: ' . $width . 'px;}';
      $output .= '</style>';
      $output .= '<script>$(function() {$(\'#content_focus_slider\').didaShow({});});</script>';
      return $output;
    } else {
      return l(t('rover', '可设置幻灯片'), 'admin/themes/setting/rover');
    }
  }
}

/**
 * 获取站点联系信息
 */
function _rover_get_contact() {
  global $conf, $is_admin; 
  
  if ($data = $conf['themes']['rover']['settings']['contact']) {
    $item = array();
    foreach ($data as $key => $value) {
      if (empty($value)) continue;
      if ($key == 'qq') {
        $value = '<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=' . $value . '&site=qq&menu=yes"><img border="0" src="http://wpa.qq.com/pa?p=2:' . $value . ':41" alt="' . t('rover', '点击这里给我发消息'). '" title="'. t('rover', '点击这里给我发消息') . '"></a>';
      }
      $item[] = array('data' => $value, 'class' => 'contact_type_' . $key);
    }
    return theme('item_list', $item);
  } else {
    return l(t('rover', '请设置站点联系信息'), 'admin/themes/setting/rover');
  }
}
