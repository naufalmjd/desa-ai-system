@echo off
title SIAP-Desa AI Server
echo ==========================================
echo    STARTING SIAP-DESA FASTAPI AI SERVER
echo ==========================================
cd /d "%~dp0\ai_server"
if not exist venv (
    echo Error: Virtual environment (venv) not found.
    echo Please make sure the venv is created.
    pause
    exit /b
)
call venv\Scripts\activate.bat
echo Installing/checking requirements...
pip install -r requirements.txt
echo Starting FastAPI application...
python main.py
pause
