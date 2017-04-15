<?php

namespace PhpDoxinizer;

class ProjectService
{
    public function getProjects()
    {
        $file = getenv('PHPDOXINIZER_CONFIG');
        if (empty($file)) {
            throw new \Exception('Configuration file not set.');
        }
        if (!file_exists($file)) {
            throw new \Exception('Configuration file not found.');
        }

        $projects = json_decode(file_get_contents($file), true);
        return $projects;
    }

    public function getProject($projectName)
    {
        $projects = $this->getProjects();
        if (!isset($projects[$projectName])) {
            throw new \Exception("Project: $projectName not found");
        }
        return $projects[$projectName];
    }

    public function authenticate($projectName, $token)
    {
        if ($token !== $this->getProject($projectName)['token']) {
            throw new \Exception('Unauthorized');
        }
        return true;
    }

    public function build($projectName, $branch)
    {
        $project = $this->getProject($projectName);

        $repoUrl = $project['repository_url'];
        $buildPath = $project['build_path'];
        $vendorBinDir = __DIR__ . '/../vendor/bin';
        $repoTmpPath = $this->tempdirnam(sys_get_temp_dir(), 'phpdoxinizer.repo.');

        error_log("Checking out $repoUrl to $repoTmpPath");

        // Checkout code.
        $git = new \PHPGit\Git();
        $git->clone($repoUrl, $repoTmpPath);
        $git->setRepository($repoTmpPath);
        $git->checkout($branch);

        error_log("Building docs");
        $cwd = getcwd();
        chdir($repoTmpPath);
        exec("$vendorBinDir/phpdox", $output, $return_var);
        chdir($cwd);
        error_log(implode("\n", $output));
        if ($return_var) {
            return false;
        }

        $docroot = __DIR__ . '/../public/projects';
        if (!file_exists("$docroot/$projectName")) {
            mkdir("$docroot/$projectName");
        }
        if (file_exists("$docroot/$projectName/$branch")) {
            exec("mv " . escapeshellarg("$docroot/$projectName/$branch") . " " . escapeshellarg("$repoTmpPath/$branch.trash"));
        }
        exec("mv " . escapeshellarg("$repoTmpPath/$buildPath") . " " . escapeshellarg("$docroot/$projectName/$branch"));
        return true;
    }

    /**
      * Like tempnam() but for directories.
      *
      * @see tempnam()
      */
    public function tempdirnam($path, $prefix)
    {
        $retries = 10;
        while ($retries-- > 0) {
            $dirname = tempnam($path, $prefix);
            @unlink($dirname);
            if (@mkdir($dirname)) {
                return $dirname;
            }
        }
        throw new \Exception("Could not create temporary directory");
    }
}
