<?php
N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorInFolderSubFolders extends N2GeneratorAbstract {

    protected $layout = 'image';

    private function trim($str, $path = true) {
        $str = ltrim(rtrim($str, '/'), '/');
        if ($path) {
            $str = '/' . $str;
        }
        return $str;
    }

    private function getSiteUrl() {
        $site_url = get_site_url();

        if (empty($site_url)) {
            $site_url = (empty($_SERVER['HTTPS']) ? "http://" : "https://") . $_SERVER['HTTP_HOST'];
        }

        return $this->trim($site_url, false);
    }

    private function getRootPath() {
        $root = '';
        $root = ABSPATH;

        if (!empty($root)) {
            $root = $this->trim($root);
        }

        return $root;
    }

    private function pathToUri($path, $media_folder = true) {
        $path = $this->trim($path);
        $root = $this->getRootPath();
        if (!empty($root) && !$media_folder) {
            $path = str_replace($root, '', $path);
            return $this->getSiteUrl() . $path;
        } else if ($media_folder) {
            return N2ImageHelper::dynamic(N2Uri::pathToUri($path));
        } else {
            return N2Uri::pathToUri($path);
        }
    }

    public function renderFields($form) {
        parent::renderFields($form);

        $filter = new N2Tab($form, 'filter', n2_('Filter'));

        new N2ElementFolders($filter, 'sourcefolder', n2_('Source folder'), '', array(
            'style' => 'width: 300px;'
        ));

        new N2ElementOnOff($filter, 'iptc', 'EXIF', 0, array(
            'rowClass' => 'n2-expert'
        ));

        new N2ElementOnOff($filter, 'remove_resize', 'Remove resized images', 0, array(
            'rowClass' => 'n2-expert',
            'tip'      => 'If you turn this option on, those files will be removed which are matching the "-[number]x[number].[extension]" pattern in the end of their names. This could be good, because in WordPress when you upload images to the Media Library, automatically new resized files will be generated, for example if the original image is image.jpg, there also will be files like image-120x120.jpg, image-500x500.jpg, image-1000x1000.jpg and images with other sizes generated, so the image is the same, only the size is different and those resized images could be removed. In this case the "Slides" setting in your generator represents all files and this removal will happen from them!'
        ));


        $_order = new N2Tab($form, 'order', n2_('Order by'));
        $order  = new N2ElementMixed($_order, 'order', n2_('Order'), '0|*|asc');

        new N2ElementList($order, 'order-1', n2_('Order'), '', array(
            'options' => array(
                '0' => n2_('None'),
                '1' => n2_('Filename'),
                '2' => n2_('Creation date')
            )
        ));

        new N2ElementRadio($order, 'order-2', n2_('order'), '', array(
            'options' => array(
                'asc'  => n2_('Ascending'),
                'desc' => n2_('Descending')
            )
        ));
    }

    function getSubFolders($folders = array(), $ready = array()) {
        $subFolders = array();
        foreach ($folders AS $folder) {
            $ready[]          = $folder;
            $subFoldersHelper = N2Filesystem::folders($folder);
            foreach ($subFoldersHelper AS $helper) {
                $subFolders[] = $folder . '/' . $helper;
            }
        }
        if (!empty($subFolders)) {
            return $this->getSubFolders($subFolders, $ready);
        } else {
            return $ready;
        }
    }

    protected function _getData($count, $startIndex) {
        $root   = str_replace('\\', '/', N2Filesystem::getImagesFolder());
        $source = str_replace('\\', '/', $this->data->get('sourcefolder', ''));
        $search = (strpos($source, "%%") !== false);
        if (substr($source, 0, 1) != '/' && substr($source, 0, 1) != '*') {
            $source = '/' . $source;
        }
        if ($search) {
            $parts          = preg_split("/[\s\/]+/", $source);
            $originalSource = $source;
            $source         = '';
            foreach ($parts AS $part) {
                if (strpos($part, "%%") !== false) {
                    $source .= $part . '/';
                } else {
                    if (substr($source, -2, 2) == '//') {
                        $source = substr($source, 0, -1);
                    }
                    break;
                }
            }
            $base = $root;
        }

        if (substr($source, 0, 1) == '*') {
            $media_folder = false;
            $source       = substr($source, 1);
            if (!N2Filesystem::existsFolder($source)) {
                N2Message::error(n2_('Wrong path. This is the default image folder path, so try to navigate from here:') . '<br>*' . $root);

                return array();
            } else {
                $root = '';
            }
        } else {
            $media_folder = true;
        }
        $baseFolder = str_replace('\\', '/', N2Filesystem::realpath($root . '/' . ltrim(rtrim($source, '/'), '/')));

        if (empty($baseFolder)) {
            N2Message::error(n2_('Folder not found.'));
            return array();
        }
        $folders = $this->getSubFolders(array( $baseFolder ));

        if ($search) {
            if (substr($originalSource, 0, 1) == '*') {
                $originalSource = substr($originalSource, 1);
            } else {
                $originalSource = $base . $originalSource;
            }
            $from           = array( '%%', '/' );
            $to             = array( '([^.]+)', '\/' );
            $pattern        = str_replace($from, $to, $originalSource);
            $pattern        = '#' . $pattern . '#';
            $matchedFolders = array();
            foreach ($folders AS $folder) {
                if (preg_match($pattern, $folder . '/')) {
                    $matchedFolders[] = $folder;
                }
            }
            $folders = $matchedFolders;
        }

        $allFiles = array();
        foreach ($folders AS $f) {
            $allFiles[$f] = N2Filesystem::files($f);
        }

        $return  = array();
        $counter = 0;
        foreach ($allFiles AS $folder => $files) {
            $counter++;
            for ($i = count($files) - 1; $i >= 0; $i--) {
                $ext        = strtolower(pathinfo($files[$i], PATHINFO_EXTENSION));
                $extensions = array( 'jpg', 'jpeg', 'png', 'svg', 'gif', 'webp' );
                if (!in_array($ext, $extensions)) {
                    array_splice($files, $i, 1);
                }
            }

            $IPTC = $this->data->get('iptc', 0) && function_exists('exif_read_data');

            $data = array();
            for ($i = 0; $i < $count && isset($files[$i]); $i++) {
                $image    = $this->pathToUri($folder . '/' . $files[$i], $media_folder);
                $data[$i] = array(
                    'image'      => $image,
                    'thumbnail'  => $image,
                    'title'      => $files[$i],
                    'name'       => preg_replace('/\\.[^.\\s]{3,4}$/', '', $files[$i]),
                    'folder'     => $folder,
                    'foldername' => basename($folder),
                    'created'    => filemtime($folder . '/' . $files[$i])
                );
                if ($IPTC) {
                    $properties = @exif_read_data($folder . '/' . $files[$i]);
                    if ($properties) {
                        foreach ($properties AS $key => $property) {
                            if (!is_array($property) && $property != '' && preg_match('/^[a-zA-Z]+$/', $key)) {
                                preg_match('/([2-9][0-9]*)\/([0-9]+)/', $property, $matches);
                                if (empty($matches)) {
                                    $data[$i][$key] = $property;
                                } else {
                                    $data[$i][$key] = round($matches[1] / $matches[2], 2);
                                }
                            }
                        }
                    }
                }
            }
            $return = array_merge($return, $data);
        }

        $return = array_slice($return, $startIndex, $count);

        if ($this->data->get('remove_resize', 0)) {
            $new = array();
            for ($i = 0; $i < count($return); $i++) {
                if (!preg_match('/[_-]\d+x\d+(?=\.[a-z]{3,4}$)/', $return[$i]['title'], $match)) {
                    $new[] = $return[$i];
                }
            }
            $return = $new;
        }

        $order = explode("|*|", $this->data->get('order', '0|*|asc'));
        switch ($order[0]) {
            case 1:
                usort($return, 'N2GeneratorInFolderSubFolders::' . $order[1]);
                break;
            case 2:
                usort($return, 'N2GeneratorInFolderSubFolders::orderByDate_' . $order[1]);
                break;
            default:
                break;
        }

        return $return;
    }

    public static function asc($a, $b) {
        return (strtolower($b['title']) < strtolower($a['title']) ? 1 : -1);
    }

    public static function desc($a, $b) {
        return (strtolower($a['title']) < strtolower($b['title']) ? 1 : -1);
    }

    public static function orderByDate_asc($a, $b) {
        return ($b['created'] < $a['created'] ? 1 : -1);
    }

    public static function orderByDate_desc($a, $b) {
        return ($a['created'] < $b['created'] ? 1 : -1);
    }
}