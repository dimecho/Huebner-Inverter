if($args[0] -eq "uninstall") {
	Start-Process "C:\EAGLE 9.2.2\unins000.exe" -Wait
}else{
    Start-Process "$env:USERPROFILE\Downloads\Autodesk_EAGLE_9.2.2_English_Win_64bit.exe" -Wait
}