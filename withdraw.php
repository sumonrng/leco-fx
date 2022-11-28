<div class="wrapper main-wrapper row" style='height:100vh'>
    <div class="col-xs-12 col-sm-6 col-sm-offset-3">
        <section class="box ">
            <header class="panel_header">
                <h2 class="title pull-left">Withdraw Fund</h2>
                <div class="actions panel_actions pull-right">
                    <a class="box_toggle fa fa-chevron-down"></a>
                    <a class="box_setting fa fa-cog" data-toggle="modal" href="#section-settings"></a>

                </div>
            </header>
            <div class="content-body">
                <?php 
						$BalanceSts=remainAmn22($member);
					?>
                <div class="row">

                    <div class="col-md-12">
                        <div class="card" id="Chose" style="background:#1a4bb5">


                            <div class="card-body TransMess">
                                <div class="form-group text-center" id="TransMess">
                                    <h3 class="text-danger"><?php echo $_SESSION['msg']; ?></h3>
                                    <h3 class="text-success"><?php echo $_SESSION['msg1']; ?></h3>
                                </div>
                                <?php
					$jhkjf=$mysqli->query("SELECT * FROM `widraw_req` WHERE `user`='".$member."' AND `pending`='0' AND `type`='Withdraw' ORDER BY `serial` DESC LIMIT 1");
						$jfdf=mysqli_num_rows($jhkjf);
						if($jfdf<1){
					?>
                                <form style="padding:15px;" action="withwraw_action.php" method="POST" name="form"
                                    id="msform" accept-charset="utf-8" autocomplete="off" novalidate="novalidate">
                                    <div class="form-group">
                                        <label for="password">Withdraw Able Amount:</label>
                                        <input type="text" class="form-control" id="avaislToken"
                                            value="$<?php echo $BalanceSts['final']; ?>"
                                            placeholder="$<?php echo $BalanceSts['final']; ?>" readonly />

                                    </div>
                                    <div class="form-group">
                                        <h3 class="text-center" id="Mess"></h3>
                                    </div>
                                    <!--<div class="form-group">
						<label for="password">Amount:</label>
						<input type="text" class="form-control" id="NumberOfToken" placeholder="Amount" />
						<span id="errorID1"></span>
					</div>-->
                                    <!--<div class="form-group" id="WalletId" style="display:none;">
						<label for="password">External Wallet ID:</label>
						<input type="text" class="form-control" id="assignTo" placeholder="External Wallet ID">
						<span id="errorID"></span>
					</div>-->
                                    <div class="form-group">
                                        <label>Select Recieve Method</label>
                                        <select class="form-control" name="mmee" id="mmee">
                                            <option value="">Select</option>
                                            <option value="Bkash">Bkash</option>
                                            <option value="Nagod">Nagod</option>
                                            <option value="Rocket">Rocket</option>
                                            <option value="Bank">Local Bank</option>
                                            <option value="Cash">Cash</option>
                                        </select>
                                    </div>

                                    <div class="form-group dddd" style="display:none;">
                                        <label class=" fff"></label>
                                        <input type="text" name="nummbb" class="form-control" id="fff"
                                            placeholder="Amount Tk">
                                    </div>

                                    <div class="form-group bbbb" style="display:none;">
                                        <label class="">Account Holder Name</label>
                                        <input type="text" name="accountHoldName" class="form-control"
                                            placeholder="Account Holder Name">
                                    </div>
                                    <div class="form-group bbbb" style="display:none;">
                                        <label>Account Number</label>
                                        <input type="text" name="accountNumber" class="form-control"
                                            placeholder="Account Number">
                                    </div>
                                    <div class="form-group bbbb" style="display:none;">
                                        <label>Bank Name</label>
                                        <input type="text" name="BankName" class="form-control" placeholder="Bank Name">
                                    </div>
                                    <div class="form-group bbbb" style="display:none;">
                                        <label>Bank Branch Name</label>
                                        <input type="text" name="BranchName" class="form-control"
                                            placeholder="Bank Branch Name">
                                    </div>
                                    <div class="form-group">
                                        <label>Amount</label>
                                        <input type="text" name="amount2" class="form-control" placeholder="Amount">
                                    </div>
                                    <div class="form-group">
                                        <label>Transanction Pin</label>
                                        <input type="password" name="pin2" class="form-control" placeholder="Pin">
                                        <input name="location" type="hidden"
                                            value="<?php echo $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>" />
                                    </div>


                                    <button type="submit" id="Profile_pIsv" class="btn btn-warning btn-block btn-clean"
                                        name="transfer">Withdraw</button>
                                    <?php }else{ 
						$hfghsf=mysqli_fetch_assoc($jhkjf);
					?>
                                </form>

                                <div class="form-group">
                                    <h3 class="alert alert-warning">Do You Want To Proceed?</h3>
                                    <p class="alert alert-warning">Transaction Request Initiated From Your Account</p>
                                </div>
                                <div class="form-group" id="MessTrans">

                                </div>

                                <button class="btn btn-warning btn-block btn-clean"
                                    data-sers="<?php echo base64_encode($hfghsf['serial']); ?>"
                                    id="Procced">Proceed</button>
                                <button class="btn btn-danger btn-block btn-clean"
                                    data-sers="<?php echo base64_encode($hfghsf['serial']); ?>"
                                    id="Cancel">Cancel</button>

                                <?php } ?>
                            </div>

                        </div>

                    </div>
                </div>
                <script>
                $(document).ready(function() {
                    $("#mmee").on("change", function() {
                        var vvhh = $(this).val();
                        if (vvhh != "") {
                            if (vvhh != "Bank") {
                                if (vvhh != "Cash") {
                                    $(".dddd").show();
                                    $(".bbbb").hide();
                                    $(".fff").text("Personal " + vvhh + " Number");
                                    $("#fff").attr("placeholder", "Personal " + vvhh + " Number");
                                } else {
                                    $(".dddd").hide();
                                    $(".bbbb").hide();
                                }
                            } else {
                                $(".dddd").hide();
                                $(".bbbb").show();
                            }
                        } else {
                            $(".dddd").hide();
                            $(".bbbb").hide();
                        }
                    });
                });
                </script>