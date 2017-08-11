<?php
require __DIR__.'/vendor/autoload.php';

use Autoversioner\Core\Application;

$gitRepo = "";
if (isset($argv[2])) {
    if (strpos($argv[2], '-')!=0) {
        die('argument not valid. Please specify -l or -r');
    }
    if ($argv[2]=='-r') {
        if (isset($argv[3])) {
            $gitRepo = $argv[3];
            if (!gitRepositoryExists($gitRepo)) {
                die('Repository does not exists');
            }
        } else {
            die('-r option must specify a git remote repository');
        }
    } elseif ($argv[2]=='-l') {
        if (isset($argv[3])) {
            $gitRepo = getGitRepositoryFromFolder($_SERVER['PWD'], $argv[3]);
        } else {
            die('-l option must specify a valid local git remote configuration');
        }
    }
} else {
    $gitRepo = getGitRepositoryFromFolder($_SERVER['PWD'], '');
}
$app = new Application($gitRepo);
$app->Run();
/**
 * @param string $path
 *
 * @return bool
 */
function isFolder($path)
{
    return is_dir($path);
}

/**
 * @param string $url
 *
 * @return string
 */
function getGitRepositoryFromFolder($url, $argumentRemote)
{
    $remotes = shell_exec("cd $url && git remote ");
    if ($remotes) {
        if (strpos($remotes, "\n", 0)>0) {
            $remotes = explode("\n", $remotes);
            array_pop($remotes);
        } else {
            $argumentRemote = $remotes;
            $remotes = [$remotes];
        }
        if (count($remotes)>1 && $argumentRemote=="") {
            die('More than one remote found in folder. Please specify what remote to use with -l option');
        }
        foreach ($remotes as $repo) {
            if ($repo == $argumentRemote) {
                $gitRemote = shell_exec(sprintf('git remote get-url %s', $argumentRemote));
            }
        }
    }
}

/**
 * @param string $url
 *
 * @return bool
 */
function gitRepositoryExists($url)
{
    $response = shell_exec(sprintf('git ls-remote %s', $url));
    if (!$response) {
        return false;
    }
    return strpos($response, 'ERROR')===false;
}
