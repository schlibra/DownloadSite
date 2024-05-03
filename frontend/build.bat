rd /s /q ..\backend\assets
del ..\backend\main.html
copy dist\index.html ..\backend\main.html
xcopy /Y dist\assets ..\backend\assets\