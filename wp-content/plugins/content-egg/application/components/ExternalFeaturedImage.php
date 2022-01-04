<?php

namespace ContentEgg\application\components;

use ContentEgg\application\admin\GeneralConfig;
use ContentEgg\application\components\FeaturedImage;

/**
 * ExternalFeaturedImage class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2019 keywordrush.com
 */
class ExternalFeaturedImage {

    const EXTERNAL_URL_META = '_cegg_thumbnail_external';
    const EXTERNAL_URL_FAKE_ID = 'cegg_external_url';

    public static function initAction()
    {
        if (GeneralConfig::getInstance()->option('external_featured_images') == 'disabled')
            return;

        \add_action('init', array(__CLASS__, 'getFakeThumbnailIds'));
        
        if (\is_admin())
            \add_filter('admin_post_thumbnail_html', array(__CLASS__, 'adminThumbnail'));
        
        \add_filter('wp_get_attachment_image_src', array(__CLASS__, 'replaceImageSrc'), 10, 4);
        \add_filter('woocommerce_product_get_image_id', array(__CLASS__, 'getFakeImageId'), 99, 2);
        \add_filter('post_thumbnail_html', array(__CLASS__, 'replaceThumbnail'), 10, 5);
        \add_action('content_egg_save_data', array(__CLASS__, 'setImage'), 13, 4);
    }

    public static function setImage($data, $module_id, $post_id, $is_last_iteration)
    {
        if (\get_post_type($post_id) == 'product')
            return;

        if (!$is_last_iteration)
            return;

        self::setExternalFeaturedImage($post_id);
    }

    public static function setExternalFeaturedImage($post_id, $item = null)
    {
        if (GeneralConfig::getInstance()->option('external_featured_images') == 'enabled_internal_priority' && self::hasInternalImage($post_id))
            return false;

        if (!$item)
        {
            $data = FeaturedImage::getData($post_id);
            if (!$data)
                return;
            $item = $data[0];
        }
        if (empty($item['img']))
            return;

        $img_url = $item['img'];
        return self::updateExternalMeta($img_url, $post_id);
    }

    public static function updateExternalMeta($url, $post_id)
    {
        $old = \get_post_meta($post_id, self::EXTERNAL_URL_META, true);
        if ($old && $old['url'] == $url)
            return true;

        $save = array();
        $save['url'] = $url;

        $width = $height = 0;
        if (ini_get('allow_url_fopen'))
            list($width, $height) = @getimagesize($url);
        $save['width'] = $width;
        $save['height'] = $height;

        return \update_post_meta($post_id, self::EXTERNAL_URL_META, $save);
    }

    public static function adminThumbnail($html)
    {
        global $post;
        if (!$external_img = \get_post_meta($post->ID, self::EXTERNAL_URL_META, true))
            return $html;

        if (empty($external_img['url']))
            return $html;

        $html .= '<div><img class="size-post-thumbnail" src="' . \esc_url($external_img['url']) . '">';
        $html .= '<p class="howto">' . __('External featured image', 'content-egg') . '</p></div>';

        return $html;
    }

    public static function getFakeImageId($value, $product)
    {
        if (GeneralConfig::getInstance()->option('external_featured_images') == 'enabled_internal_priority' && self::hasInternalImage($product->get_id()))
            return $value;

        $product_id = $product->get_id();
        if (\get_post_meta($product_id, self::EXTERNAL_URL_META, true))
            return self::EXTERNAL_URL_FAKE_ID . '%' . $product_id;
        else
            return $value;
    }

    public static function getFakeThumbnailIds()
    {
        $post_types = GeneralConfig::getInstance()->option('post_types');
        foreach ($post_types as $post_type)
        {
            \add_filter("get_{$post_type}_metadata", array(__CLASS__, 'getFakeThumbnailId'), 10, 4);
        }
    }

    public static function getFakeThumbnailId($value, $object_id, $meta_key, $single)
    {
        if (GeneralConfig::getInstance()->option('external_featured_images') == 'enabled_internal_priority' && self::hasInternalImage($object_id))
            return $value;

        if ($meta_key != '_thumbnail_id')
            return $value;

        if (\get_post_meta($object_id, self::EXTERNAL_URL_META, true))
            return self::EXTERNAL_URL_FAKE_ID . '%' . $object_id;
        else
            return $value;
    }

    public static function replaceImageSrc($image, $attachment_id, $size, $icon)
    {
        if (!strstr($attachment_id, self::EXTERNAL_URL_FAKE_ID . '%'))
            return $image;

        $parts = explode('%', $attachment_id);
        $post_id = $parts[1];

        if (!$external_img = \get_post_meta($post_id, self::EXTERNAL_URL_META, true))
            return $image;

        $external_url = $external_img['url'];

        if ($image_size = self::getImageSize($size))
            return array($external_url, $image_size['width'], $image_size['height'], $image_size['crop']);
        else
        {
            if (!empty($external_img['width']))
                $width = $external_img['width'];
            else
                $width = 800;

            if (!empty($external_img['height']))
                $height = $external_img['height'];
            else
                $height = 600;

            return array($external_url, $width, $height, false);
        }
    }

    public static function getImageSize($size)
    {
        if (is_array($size))
        {
            return array(
                'width' => isset($size[0]) ? $size[0] : null,
                'height' => isset($size[1]) ? $size[1] : null,
                'crop' => isset($size[2]) ? $size[2] : null,
            );
        }

        global $_wp_additional_image_sizes;
        if (isset($_wp_additional_image_sizes[$size]))
            return $_wp_additional_image_sizes[$size];

        $default = array('thumbnail', 'medium', 'medium_large', 'large');
        if (in_array($size, $default))
        {
            return array(
                'width' => \get_option("{$size}_size_w"),
                'height' => \get_option("{$size}_size_h"),
                'crop' => \get_option("{$size}_crop"),
            );
        }
        return array();
    }

    public static function replaceThumbnail($html, $post_id, $post_thumbnail_id, $size, $attr)
    {
        if (!$external_img = \get_post_meta($post_id, self::EXTERNAL_URL_META, true))
            return $html;

        if (GeneralConfig::getInstance()->option('external_featured_images') == 'enabled_internal_priority' && self::hasInternalImage($post_id))
            return $html;

        $url = $external_img['url'];        
        $alt = \get_post_field('post_title', $post_id);
        $class = 'cegg-external-img wp-post-image';
        $attr = array('alt' => $alt, 'class' => $class);
        $attr = \apply_filters('wp_get_attachment_image_attributes', $attr, $size);
        $attr = array_map('esc_attr', $attr);
        $html = sprintf('<img src="%s"', esc_url($url));
        foreach ($attr as $name => $value)
        {
            $html .= " $name=" . '"' . $value . '"';
        }
        $html .= ' />';
        return $html;
    }

    public static function hasInternalImage($object_id)
    {
        $meta_type = 'post';
        $meta_key = '_thumbnail_id';

        $meta_cache = \wp_cache_get($object_id, $meta_type . '_meta');
        if (!$meta_cache)
        {
            $meta_cache = \update_meta_cache($meta_type, array($object_id));
            $meta_cache = $meta_cache[$object_id];
        }

        if (isset($meta_cache[$meta_key]))
            $meta_value = $meta_cache[$meta_key][0];
        else
            $meta_value = false;

        if ($meta_value)
            return true;
        else
            return false;
    }

}
