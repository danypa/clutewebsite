<?php

  namespace WebpConverter\Settings;

  use WebpConverter\Media\Htaccess;

  class Errors
  {
    private $cache    = null;
    private $filePath = WEBPC_PATH . '/resources/components/errors/%s.php';

    public function __construct()
    {
      add_filter('webpc_server_errors', [$this, 'getServerErrors']);
    }

    /* ---
      Functions
    --- */

    public function getServerErrors()
    {
      if ($this->cache !== null) return $this->cache;

      $this->cache = $this->loadErrorMessages();
      return $this->cache;
    }

    private function loadErrorMessages()
    {
      $errors = $this->getErrorsList();
      $list   = [];
      foreach ($errors as $error) {
        ob_start();
        include sprintf($this->filePath, str_replace('_', '-', $error));
        $list[$error] = ob_get_contents();
        ob_end_clean();
      }
      return $list;
    }

    private function getErrorsList()
    {
      $errors = [];

      if ($this->ifLibsAreInstalled() !== true) {
        $errors[] = 'libs_not_installed';
      } else if ($this->ifLibsSupportWebp() !== true) {
        $errors[] = 'libs_without_webp_support';
      }
      if ($errors) return $errors;

      if ($this->ifSettingsAreCorrect() !== true) {
        $errors[] = 'settings_incorrect';
      }
      if ($errors) return $errors;

      if ($this->ifRestApiIsEnabled() !== true) {
        $errors[] = 'rest_api_disabled';
      }
      if ($this->ifUploadsPathExists() !== true) {
        $errors[] = 'path_uploads_unavailable';
      } else if ($this->ifHtaccessIsWriteable() !== true) {
        $errors[] = 'path_htaccess_not_writable';
      }
      if ($this->ifPathsAreDifferent() !== true) {
        $errors[] = 'path_webp_duplicated';
      } else if ($this->ifWebpPathIsWriteable() !== true) {
        $errors[] = 'path_webp_not_writable';
      }
      if ($errors) return $errors;

      if ($this->ifRedirectsAreWorks() !== true) {
        if ($this->ifBypassingApacheIsActive() === true) {
          $errors[] = 'bypassing_apache';
        } else {
          $errors[] = 'rewrites_not_working';
        }
      } else if ($this->ifRedirectsAreCached() === true) {
        $errors[] = 'rewrites_cached';
      }

      return $errors;
    }

    private function ifLibsAreInstalled()
    {
      return (extension_loaded('gd') || (extension_loaded('imagick') && class_exists('\Imagick')));
    }

    private function ifLibsSupportWebp()
    {
      $methods = apply_filters('webpc_get_methods', []);
      return (count($methods) > 0);
    }

    private function ifSettingsAreCorrect()
    {
      $settings = apply_filters('webpc_get_values', [], true);
      if ((!isset($settings['extensions']) || !$settings['extensions'])
        || (!isset($settings['dirs']) || !$settings['dirs'])
        || (!isset($settings['method']) || !$settings['method'])
        || (!isset($settings['quality']) || !$settings['quality'])) return false;

      return true;
    }

    private function ifRestApiIsEnabled()
    {
      return ((apply_filters('rest_enabled', true) === true)
        && (apply_filters('rest_jsonp_enabled', true) === true)
        && (apply_filters('rest_authentication_errors', true) === true));
    }

    private function ifUploadsPathExists()
    {
      $path = apply_filters('webpc_uploads_path', '');
      return (is_dir($path) && ($path !== ABSPATH));
    }

    private function ifHtaccessIsWriteable()
    {
      $pathDir  = apply_filters('webpc_uploads_path', '');
      $pathFile = $pathDir . '/.htaccess';
      if (file_exists($pathFile)) return (is_readable($pathFile) && is_writable($pathFile));
      else return is_writable($pathDir);
    }

    private function ifPathsAreDifferent()
    {
      $pathUploads = apply_filters('webpc_uploads_path', '');
      $pathWebp    = apply_filters('webpc_uploads_webp', '');
      return ($pathUploads !== $pathWebp);
    }

    private function ifWebpPathIsWriteable()
    {
      $path = apply_filters('webpc_uploads_webp', '');
      return (is_dir($path) || is_writable(dirname($path)));
    }

    private function ifRedirectsAreWorks()
    {
      do_action('webpc_convert_paths', apply_filters('webpc_attachment_paths', [
        WEBPC_PATH . 'public/img/icon-test.png',
      ], true));

      $fileSize = filesize(WEBPC_PATH . 'public/img/icon-test.png');
      $fileWebp = $this->getFileSizeByUrl(WEBPC_URL . 'public/img/icon-test.png', [
        'Accept: image/webp',
        'Referer: ' . WEBPC_URL,
      ]);

      return ($fileWebp < $fileSize);
    }

    private function getFileSizeByUrl($url, $headers = [])
    {
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_TIMEOUT, 10);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      $response = curl_exec($ch);
      $code     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close($ch);

      return ($code === 200) ? strlen($response) : 0;
    }

    private function ifBypassingApacheIsActive()
    {
      add_filter('webpc_get_values',        [$this, 'addCustomExtension']);
      add_filter('webpc_gd_create_methods', [$this, 'addMethodForCustomExtension']);
      do_action(Htaccess::ACTION_NAME, true);

      do_action('webpc_convert_paths', apply_filters('webpc_attachment_paths', [
        WEBPC_PATH . 'public/img/icon-test.png2',
      ], true));

      $filePng  = $this->getFileSizeByUrl(WEBPC_URL . 'public/img/icon-test.png', [
        'Accept: image/webp',
        'Referer: ' . WEBPC_URL,
      ]);
      $filePng2 = $this->getFileSizeByUrl(WEBPC_URL . 'public/img/icon-test.png2', [
        'Accept: image/webp',
        'Referer: ' . WEBPC_URL,
      ]);

      remove_filter('webpc_get_values',        [$this, 'addCustomExtension']);
      remove_filter('webpc_gd_create_methods', [$this, 'addMethodForCustomExtension']);
      do_action(Htaccess::ACTION_NAME, true);

      return ($filePng > $filePng2);
    }

    public function addCustomExtension($settings)
    {
      $settings['extensions'][] = 'png2';
      return $settings;
    }

    public function addMethodForCustomExtension($methods)
    {
      $methods['imagecreatefrompng'][] = 'png2';
      return $methods;
    }

    private function ifRedirectsAreCached()
    {
      $fileWebp     = $this->getFileSizeByUrl(WEBPC_URL . 'public/img/icon-test.png', [
        'Accept: image/webp',
        'Referer: ' . WEBPC_URL,
      ]);
      $fileOriginal = $this->getFileSizeByUrl(WEBPC_URL . 'public/img/icon-test.png');

      return (($fileWebp > 0) && ($fileWebp === $fileOriginal));
    }
  }