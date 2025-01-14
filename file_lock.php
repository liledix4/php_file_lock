<?php
final class FileLock {
    protected static $result = [];

    protected static function addLock(string $fileLockFull)
    {
        $a1 = preg_split('/\r?\n/', strtolower(file_get_contents($fileLockFull)));
        foreach ($a1 as $a1v)
        {
            $a2 = preg_split('/:\s*/', $a1v);
            $a3 = preg_split('/,\s*/', $a2[1]);
            self::$result[$a2[0]] = $a3;
        }
    }

    final static function getData(string $relativeFilePath, string $directoryRoot = null, string $lockFileExtension = '.lock'): array
    {
        if ($directoryRoot === null)
            $directoryRoot = __DIR__;

        $fileLockRelative = "$relativeFilePath$lockFileExtension";
        $fileLockFull = "$directoryRoot/$fileLockRelative";
        self::$result = [];

        preg_match('/.*\//', $fileLockRelative, $directoryRelative);
        $directoryRelative = $directoryRelative[0] ?? '';

        // Check ALL subdirectories for ".lock" file
        $directoryRelativeArray = array_filter( preg_split('/\//', $directoryRelative) );
        $temp = '';
        foreach ($directoryRelativeArray as $value)
        {
            if ($temp !== '')
                $temp = "$temp/";
            $temp = "$temp$value";
            $tempLock = "$temp/$lockFileExtension";
            $tempLockFull = "$directoryRoot/$tempLock";
            if (file_exists($tempLockFull))
                self::addLock($tempLockFull);
        }

        if (file_exists($fileLockFull))
            self::addLock($fileLockFull);

        return self::$result;
    }
}