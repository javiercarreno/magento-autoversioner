<?php


namespace Autoversioner\Helpers;

class ErrorHandler
{
    /**
     * @param \Exception $ex
     */
    public static function HandleError(\Exception $ex)
    {
        echo $ex->getMessage().". So: ";
        echo "\033[1;33m\n___  ___  _____   __  _____ _   _  _____  ______ ___________  _____  _____  ______ _____   _    _ _____ _____ _   _  __   _______ _   _";
        echo "\033[1;33m\n|  \/  | / _ \ \ / / |_   _| | | ||  ___| |  ___|  _  | ___ \/  __ \|  ___| | ___ \  ___| | |  | |_   _|_   _| | | | \ \ / /  _  | | | |";
        echo "\033[1;33m\n| .  . |/ /_\ \ V /    | | | |_| || |__   | |_  | | | | |_/ /| /  \/| |__   | |_/ / |__   | |  | | | |   | | | |_| |  \ V /| | | | | | |";
        echo "\033[1;33m\n| |\/| ||  _  |\ /     | | |  _  ||  __|  |  _| | | | |    / | |    |  __|  | ___ \  __|  | |/\| | | |   | | |  _  |   \ / | | | | | | |";
        echo "\033[1;33m\n| |  | || | | || |     | | | | | || |___  | |   \ \_/ / |\ \ | \__/\| |___  | |_/ / |___  \  /\  /_| |_  | | | | | |   | | \ \_/ / |_| |";
        echo "\033[1;33m\n\_|  |_/\_| |_/\_/     \_/ \_| |_/\____/  \_|    \___/\_| \_| \____/\____/  \____/\____/   \/  \/ \___/  \_/ \_| |_/   \_/  \___/ \___/ ";
        echo "\033[0m\n";
    }
}