<?php
	include_once("common.php");
    
    if(isset($_POST["flash"]))
    {
		$os = detectOS();
		
		$file = str_replace(" ", "%", getcwd()) . "/firmware/stm32_test.bin";
		
		//print_r($_FILES);
		if(isset($_FILES["file"])) {
			$file = $_FILES['file']['tmp_name'];
		}

        $interface = urldecode($_POST["interface"]);

        if ($os === "windows") {
            $file = str_replace("/","\\",$file);
            $interface = str_replace("/","\\",$interface);
        }

        if (strpos($interface, "stlink-v2") !== false) {
            $command = runCommand("stlink", $file, $os, 0);
        }else{
            $command = runCommand("openocd", $file. " " .$interface, $os, 0);
        }
        exec($command, $output, $return);

        echo "\n$command\n";
        foreach ($output as $line) {
            echo "$line\n";
        }
    }else{
?>
<!DOCTYPE html>
<html>
    <head>
        <?php include "header.php" ?>
        <script src="js/potentiometer.js"></script>
        <script src="js/jquery.knob.js"></script>
        <script src="js/firmware.js"></script>
        <script src="js/test.js"></script>
    </head>
    <body>
        <div class="navbar navbar-expand-lg fixed-top navbar-light bg-light" id="mainMenu"></div>
        <div class="row mt-5"></div>
        <div class="row mt-5"></div>
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="container bg-light">
                        <div class="row">
                            <div class="col">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item"><a class="nav-link" href="#tabAnalog">Analog</a></li>
                                    <li class="nav-item d-none"><a class="nav-link" href="#tabDigital">Digital</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#tabHardware">Hardware</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="container" id="tabAnalog">
                            <div class="row mt-4"></div>
                            <div class="row">
                                <div class="col">
                                    <button type="button" class="btn btn-danger" onClick="stopTest()"><i class="icons icon-cancel"></i> Stop</button>
                                </div>
                                <div class="col">
                                    <button type="button" class="btn btn-success" onClick="startTest()"><i class="icons icon-ok"></i> Start</button>
                                </div>
                            </div>
                            <div class="row mt-4"></div>
                            <div class="row">
                                <div class="col">Protection</div>
                                <div class="col"><div class="circle-size-1 circle-grey" id="din_mprot"></div></div>
                            </div>
                            <div class="row">
                                <div class="col">Emergency</div>
                                <div class="col"><div class="circle-size-1 circle-grey" id ="din_emcystop"></div></div>
                            </div>
                            <div class="row">
                                <div class="col">Brake</div>
                                <div class="col"><div class="circle-size-1 circle-grey" id ="din_brake"></div></div>
                            </div>
                            <div class="row">
                                <div class="col">Start</div>
                                <div class="col"><div class="circle-size-1 circle-grey" id ="din_start"></div></div>
                            </div>
                            <div class="row">
                                <div class="col">Forward</div>
                                <div class="col"><div class="circle-size-1 circle-grey" id ="din_forward"></div></div>
                            </div>
                            <div class="row">
                                <div class="col">Reverse</div>
                                <div class="col"><div class="circle-size-1 circle-grey" id ="din_reverse"></div></div>
                            </div>
                            <div class="row">
                                <div class="col">Cruise</div>
                                <div class="col"><div class="circle-size-1 circle-grey" id ="din_cruise"></div></div>
                            </div>
                            <div class="row">
                                <div class="col">Potentiometer</div>
                            </div>
                            <div class="row text-center">
                                <div class="col">
                                    <input class="knob" data-displayinput="true" data-min="0" data-max="100" data-fgcolor="#222222" data-bgcolor="#FFFFFF" value="0">
                                </div>
                            </div>
							<div class="row mt-4"></div>
                        </div>
                        <div class="container" id="tabDigital" style="display: none;">
                            <div class="row mt-4"></div>
							 <div class="row">
                                <div class="col">
                                    <button type="button" class="btn btn-primary" onClick="location.href='can.php'"><i class="icons icon-list"></i> CAN Mapping</button>
                                </div>
                            </div>
							<div class="row mt-4"></div>
                            <div class="row">
                                <div class="col">CAN Interface:</div>
                                <div class="col"><select name="can" class="form-control" id="can-interface"></select></div>
                            </div>
							<div class="row mt-4"></div>
							<div class="row">
                                <div class="col">Receive:</div>
                                <div class="col">Send:</div>
                            </div>
							<div class="row">
                                <div class="col">
									<textarea type="text" id="can-receive" class="md-textarea form-control" rows="7" readonly></textarea>
								</div>
                                <div class="col">
									<div class="input-group w-100">
                                        <span class = "input-group-addon w-75">
										    <input type="text" class="form-control" id="can-send" />
										</span>
                                        <span class = "input-group-addon w-25 text-center">
											<button class="btn btn-primary" type="button"><i class="icons icon-select"></i> Send</button>
										</span>
                                    </div>
									<br>
									<textarea type="text" id="can-send-log" class="md-textarea form-control" rows="4" readonly></textarea>
								</div>
                            </div>
							<div class="row mt-4"></div>
                        </div>
                        <div class="container" id="tabHardware" style="display: none;">
                            <div class="row mt-4"></div>
                            <div class="row">
                                <div class="col">
                                    <button type="button" class="btn btn-primary" onClick="window.open('https://github.com/jsphuebner/stm32-test')"><i class="icons icon-download"></i> Download Test Firmware</button>
                                </div>
                                <div class="col">
									<form enctype="multipart/form-data" action="test.php" method="POST" id="firmwareForm">
                                        <input name="firmware" type="file" class="file" hidden onchange="setFirmwareFile()"/>
                                        <button class="browse btn btn-primary" type="button"><i class="icons icon-select"></i> Select stm32_test.bin</button>
                                    </form>
                                </div>
                            </div>
                            <div class="row mt-4"></div>
							<div class="row">
                                <div class="col">Hardware:</div>
                                <div class="col"><select name="hardware" class="form-control" id="hardware-version"></select></div>
							</div>
                            <div class="row">
                                <div class="col">Debugger:</div>
                                <div class="col"><select name="debugger" class="form-control" id="debugger-interface"></select></div>
                            </div>
                            <div class="row">
                                <div class="col">Serial:</div>
                                <div class="col"><select name="serial" class="form-control" id="serial2-interface"></select></div>
                            </div>
							<div class="row">
                                <div class="col">Firmware:</div>
                                <div class="col"><input type="text" class="form-control" id="firmware-file-path" value="firmware/stm32_test.bin" readonly /></div>
                            </div>
							<div class="row mt-4"></div>
                            <div class="row">
                                <div class="col">
                                    <button class="btn btn-success" type="button" onClick="$('#hwtestconfirm').modal()"><i class="icons icon-chip"></i> Flash Test Firmware</button>
                                </div>
                                <div class="col">
									<button class="btn btn-success" type="button" onClick="hardwareTestRun()"><i class="icons icon-test"></i> Run Test Results</button>
                                </div>
                            </div>
                            <div class="row mt-4"></div>
							<div class="row">
                                <div class="col">
                                    <center><div class="spinner-border text-dark d-none"></div></center>
                                </div>
                            </div>
							<div class="row mt-4"></div>
                            <div class="row">
                                <div class="col">
									<div class="container" id="hardware-results"></div>
                                    <center>
                                        <img src="" id="hardware-image" class="img-thumbnail rounded" />
                                    </center>
                                </div>
                            </div>
                            <div class="row mt-4"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="hwtestconfirm" tabindex="-1" role="dialog" aria-labelledby="hwtestconfirmTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg bg-light" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="hwtestconfirmTitle">Warning</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h4><i class="icons icon-alert"></i> Warning: This test will <font color=red>ERASE FLASH</font></h4>
                    <br>Debugger (JTAG/ST-Link) <b>AND</b> Serial (UART) must be connected.<br><br>
                        After the test is complete you will need to flash original bootloader and firmware.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="icons icon-cancel"></i> Cancel</button>
                    <button type="button" class="btn btn-success" data-dismiss="modal" onClick="startTest()"><i class="icons icon-ok"></i> Continue Test</button>
                </div>
            </div>
        </div>
        <?php include "footer.php" ?>
    </body>
</html>
<?php } ?>