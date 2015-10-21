<?php

/**
 * Class Scan
 *
 * @description Multi-threaded web directory scanner.
 * @author Samed Düzçay <samedduzcay@gmail.com>
 * Uses bruteforce method for creating possible paths.
 * @license GNU GENERAL PUBLIC LICENSE. Version 3, 29 June 2007. Look at the LICENSE file for further information.
 * @version 1.0
 *
 */
class Scan
{
    /**
     * @var int Maximum thread count, default is 80. Works best between 70~100 threads
     */
    private $threadCount = 80;
    /**
     * @var string Base URL
     */
    private $baseURL;
    /**
     * @var int Minimum path length
     */
    private $minPathLength;
    /**
     * @var int Maximum path length
     */
    private $maxPathLength;
    /**
     * @var array Special paths for checking, these are checked first
     */
    private $specialPaths;

    /**
     * Auto class loader method
     * @param string $classname
     */
    public function autoload($classname)
    {
        include_once("./" . $classname . ".php");
    }

    /**
     * @return int Minimum path length
     */
    public function getMinPathLength()
    {
        return $this->minPathLength;
    }

    /**
     * @param int $minPathLength
     */
    public function setMinPathLength($minPathLength)
    {
        $this->minPathLength = $minPathLength;
    }

    /**
     * @return int Maximum path length
     */
    public function getMaxPathLength()
    {
        return $this->maxPathLength;
    }

    /**
     * @param int $maxPathLength
     */
    public function setMaxPathLength($maxPathLength)
    {
        $this->maxPathLength = $maxPathLength;
    }

    /**
     * @return string Base URL
     */
    public function getBaseURL()
    {
        return $this->baseURL;
    }

    /**
     * @param string $baseURL
     */
    public function setBaseURL($baseURL)
    {
        $this->baseURL = $baseURL;
    }

    /**
     * @return mixed Special paths to scan, set by user
     */
    public function getSpecialPaths()
    {
        return $this->specialPaths;
    }

    /**
     * @param array $specialPaths Special paths that will be scanned first
     */
    public function setSpecialPaths($specialPaths)
    {
        $this->specialPaths = (array)$specialPaths;
    }

    /**
     * @return int Multi-thread thread count
     */
    public function getThreadCount()
    {
        return $this->threadCount;
    }

    /**
     * @param int $threadCount
     */
    public function setThreadCount($threadCount)
    {
        $this->threadCount = $threadCount;
    }

    public function __construct($baseURL, $minPathLength, $maxPathLength, $specialPaths = null)
    {
        spl_autoload_register('Scan::autoload');
        if ((int)$minPathLength <= 0) {
            throw new Exception("minPathLength value should be an integer greater than 0.");
        } else {
            $this->minPathLength = (int)$minPathLength;
        }
        $this->maxPathLength = (int)$maxPathLength;
        $this->baseURL = $baseURL;
        $this->specialPaths = $specialPaths;
    }

    public function scan()
    {
        $pathGenerator = new BruteforceGenerator($this->minPathLength, $this->maxPathLength);
        $threadPool = array();

        while ($pathGenerator->jobDone !== true) {
            for ($i = 0; $i < $this->threadCount; $i++) {
                if (is_array($this->specialPaths) && !empty($this->specialPaths)) {
                    $path = $this->specialPaths[0];
                    unset($this->specialPaths[0]);
                    $this->specialPaths = array_values($this->specialPaths);
                } elseif (($path = $pathGenerator->getNewData()) === false)
                    break;
                $threadPool[$i] = new ScanThreader($this->baseURL, $path);
                $threadPool[$i]->start();
            }

            for ($i = 0; $i < count($threadPool); $i++) {
                $threadPool[$i]->join();
                if ($threadPool[$i]->result >= 100 && $threadPool[$i]->result < 400)
                    echo $threadPool[$i]->getFullURL() . " Found. - Response Code: " . $threadPool[$i]->result . PHP_EOL;
            }

        }
    }

}