@echo off
setlocal EnableDelayedExpansion

REM If tests passed (errorlevel 0), proceed with publishing
if %errorlevel% equ 0 (
    echo Tests passed. Proceeding with publish...
    
    REM Create a new git tag based on the version in composer.json
    for /f "tokens=2 delims=:," %%i in ('findstr "version" composer.json') do (
        set VERSION=%%~i
        set VERSION=!VERSION:"=!
        set VERSION=!VERSION: =!
    )
    
    if defined VERSION (
        echo Publishing version !VERSION!...
        git tag -a v!VERSION! -m "Version !VERSION!"
        git push origin v!VERSION!
        
        echo Tagged and pushed version v!VERSION!
        echo Now visit https://packagist.org/packages/submit
        echo and submit the GitHub repository URL: https://github.com/lotcz/zavadil-php-common
    ) else (
        echo Could not determine version from composer.json
        exit /b 1
    )
) else (
    echo Tests failed. Fix the issues before publishing.
    exit /b 1
)
