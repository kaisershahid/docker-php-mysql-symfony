<?php
/**
 * Copy files to another directory, ignoring .git. Usage:
 *
 * php copyme.php destination
 */

class Copier {
    /**
     * @var string
     */
    private $src;
    /**
     * @var string
     */
    private $dst;
    /**
     * @var string
     */
    private $app;

    /**
     * Copier constructor.
     * @param string $src
     * @param string $dst
     */
    public function __construct($src, $dst) {
        if (file_exists($dst) && !is_dir($dst)) {
            echo "x $dst isn't a directory!\n";
            exit(1);
        } else if (!file_exists($dst)) {
            if (!mkdir($dst, 0777, true)) {
                echo "x $dst could not be created\n";
                exit(1);
            }
        }

        $this->src = $src;
        $this->dst = $dst;
    }

    public function setOpts(array $opts) : Copier {
        if (isset($opts['app'])) {
            $this->app = $opts['app'];
            echo "! app set to: {$this->app}\n";
        }

        return $this;
    }

    public function exec() {
        echo ">> copying to {$this->dst}\n";
        $this->processDir($this->src, $this->dst);
        echo "<< finished copying to {$this->dst}\n";
    }

    const SKIP_ENTRIES = ['.', '..', '.git', '.idea', 'setup.php', 'readme.md'];

    public function processDir($src, $dst) {
        $dir = dir($src);
        $entry = $dir->read();
        while ($entry) {
            $file  = $entry;
            $entry = $dir->read();
            if (in_array($file, self::SKIP_ENTRIES)) {
                continue;
            }

            $srcPath = "$src/$file";
            $dstPath = "$dst/$file";
            if (is_file($srcPath)) {
                $this->processFile($srcPath, $dstPath);
            } else {
                if (!is_dir($dstPath)) {
                    mkdir($dstPath);
                }

                $this->processDir($srcPath, $dstPath);
            }
        }
    }

    public function processFile($srcPath, $dstPath) {
        if ($this->app) {
            $dump = file_get_contents($srcPath);
            $dump = str_replace('YOUR_APP', $this->app, $dump);
            file_put_contents($dstPath, $dump);
        } else {
            copy($srcPath, $dstPath);
        }

        chmod($dstPath, fileperms($srcPath));
    }
}

$help = <<<HELP
# USAGE: php setup.php [-app app-name] destination
#
# -app: your application's name. if specified, YOUR_APP in the code will be
#       replaced with this.

HELP;

echo $help;

array_shift($argv);

$opts = [];
$lkey = null;
$dst = null;
foreach ($argv as $arg) {
    if ($arg[0] == '-') {
        $lkey = substr($arg, 1);
    } else if ($lkey !== null) {
        $opts[$lkey] = $arg;
        $lkey = null;
    } else {
        $dst = $arg;
    }
}

if (!$dst) {
    echo "x must specify a destination directory\n";
    exit(1);
}

(new Copier(__DIR__, $dst))
    ->setOpts($opts)
    ->exec();
