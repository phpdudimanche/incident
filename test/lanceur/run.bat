@echo off
del /f D:\code-source\dev-01\test\rapport\rapport-selenium.xml
start selenium-rc.bat
timeout 5 >nul
start phpunit-selenium.bat
exit