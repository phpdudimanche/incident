D:\UniServerZ\core\php54\php.exe D:\TEST-PHP\vendor\phpunit\phpunit\phpunit --configuration D:\code-source\dev-01\test\test-selenium
cmd /c start http://localhost:4444/selenium-server/driver?cmd=shutDownSeleniumServer
timeout 80 >nul
TASKKILL /IM chrome.exe /F
#PAUSE
exit