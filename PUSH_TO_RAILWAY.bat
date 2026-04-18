@echo off
echo.
echo ===========================================
echo   TRAVEL AGENCY - PUSHING TO RAILWAY
echo ===========================================
echo.

cd /d "c:\XAMPP\htdocs\travel_agency"

echo [1/3] Adding files...
git add .

echo [2/3] Committing changes...
git commit -m "Final Fix: Robust database variables"

echo [3/3] Pushing to cloud...
git push

echo.
echo ===========================================
echo   FINISH! Please check Railway Dashboard.
echo ===========================================
pause
