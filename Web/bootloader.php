<?php
	include_once("common.php");
	error_reporting(E_ERROR | E_PARSE);
    $os = detectOS();
	
    if(isset($_GET["ajax"])){
		$command = runCommand("openocd", urldecode($_GET["file"]). " " .urldecode($_GET["interface"]),$os);
        exec($command, $output, $return);
		echo sys_get_temp_dir();
        echo "\n$command\n";
        foreach ($output as $line) {
            echo "$line\n";
        }
    }else{
?>
<!DOCTYPE html>
<html>
    <head>
        <?php include "header.php"; ?>
		<script src="js/firmware.js"></script>
    </head>
    <body>
        <div class="container">
            <?php include "menu.php" ?>
            <br>
            <div class="row">
                <div class="col">
                    <table class="table table-active bg-light table-bordered">
                        <tr>
                            <td>
                                <button type="button" class="btn btn-primary" onClick="window.open('https://github.com/jsphuebner/tumanako-inverter-fw-bootloader')"><i class="glyphicon glyphicon-circle-arrow-down"></i> Download Bootloader</button>
                            </td>
                        </tr>
                    </table>
                    <table class="table table-active bg-light table-bordered">
                        <?php if(isset($_FILES["firmware"])){ ?>
                            <tr>
                                <td>
									<script>
										$(document).ready(function() {
                                            var progressBar = $("#progressBar");
                                            for (i = 0; i < 100; i++) {
                                                setTimeout(function(){ progressBar.css("width", i + "%"); }, i*2000);
                                            }
                                            $.ajax({
                                                type: "GET",
                                                url:
                                                <?php
                                                    //$tmp_dir = ini_get('upload_tmp_dir') ? ini_get('upload_tmp_dir') : sys_get_temp_dir();
                                                    echo "'";
                                                    echo "bootloader.php?ajax=1";
													$ocd_interface = $_POST["interface"];
                                                    if ($os === "mac") {
                                                        $ocd_file = "/tmp/" .$_FILES['firmware']["name"];
													}elseif ($os === "windows") {
														$ocd_file = sys_get_temp_dir(). "\\" .$_FILES['firmware']["name"];
														$ocd_interface = str_replace("/","\\",$ocd_interface);
                                                    }else{
                                                        $ocd_file = sys_get_temp_dir(). "/" .$_FILES['firmware']["name"];
                                                    }
                                                    move_uploaded_file($_FILES['firmware']['tmp_name'], $ocd_file);
                                                    echo "&file=" .urlencode($ocd_file);
                                                    echo "&interface=" .urlencode($ocd_interface);
                                                    echo "'";
                                                ?>,
                                                success: function(data){
                                                    //console.log(data);
                                                    progressBar.css("width","100%");
                                                    $("#output").append($("<pre>").append(data));
                                                    if(data.indexOf("shutdown command invoked") !=-1)
                                                    {
                                                        $.notify({ message: "Bootloader Complete" },{ type: 'success' });
                                                        $.notify({ message: "...Next Flash Firmware" },{ type: 'warning' });
                                                        setTimeout( function (){
                                                            window.location.href = "firmware.php";
                                                        },8000);
                                                    }
                                                }
                                            });
                                        });
                                    </script>
                                    <div class="progress progress-striped active">
                                        <div class="progress-bar" style="width:0%" id="progressBar"></div>
                                    </div>
                                    <div id="output"></div>
                                </td>
                            </tr>
                        <?php }else{ ?>
                           <script>
                                $(document).ready(function() {
                                    for (var i = 0; i < jtag_interface.length; i++) {
                                        $("#firmware-interface").append($("<option>",{value:jtag_interface[i],selected:'selected'}).append(jtag_interface[i]));
                                    }
                                    $("#firmware-interface").prop('selectedIndex', 0);
									$(".loader").hide();
									$(".input-group-addon").show();
                                    setInterfaceImage();
                                });
                            </script>
                            <tr>
                                <td>
									<center>
									<div class="loader"></div>
                                    <div class="input-group w-100">
                                        <span class = "input-group-addon hidden w-75">
                                            <select name="interface" class="form-control" form="firmwareForm" onchange="setInterfaceImage()" id="firmware-interface"></select>
                                        </span>
                                        <span class = "input-group-addon hidden w-25">
											<center>
												<button class="browse btn btn-primary" type="button"><i class="glyphicon glyphicon-search"></i> Select stm32_loader.bin</button>
											</center>
										</span>
                                    </div>
                                    <br><br><h2 id="jtag-name"></h2><br>
                                    <img src="" id="jtag-image" class="rounded" />
									</center>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>
        <form enctype="multipart/form-data" action="bootloader.php" method="POST" id="firmwareForm">
            <input name="firmware" type="file" class="file" hidden onchange="firmwareUpload()" />
        </form>
    </body>
</html>
<?php } ?>