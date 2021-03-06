<!-- Fixed navbar -->
<?php $currUrl = trim($_SERVER['REQUEST_URI'],'/'); ?>
<div class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">


        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#"><img src="images/jmvi_logo.png" class="nav_logo"/></a>
        </div>


        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">  
                <?php
                    $extras = new Extras();                    
                    $reservedRentUrl = 'reservedproperty.php?stype=0'; 
                    $reservedSaleUrl = 'reservedproperty.php?stype=1'; 
                ?>
				<?php /*
                <li class=' <?= $currUrl == 'realestates.php' ? 'active':''  ?>'><a href="realestates.php">Go to Dashboard <span class="caret"></span></a></li>                
                */ ?>
				<li class='<?= $currUrl == in_array($currUrl, ['featured.php']) ? 'active':''  ?>'><a href="featured.php">Featured</a></li>                
                <li class="">
                    <div class="dropdown">
                        <span class="dropdown-toggle" type="button" data-toggle="dropdown">For Rent
                        <span class="caret"></span></span>
                        <ul class="dropdown-menu">                            
                            <li class='<?= $currUrl == in_array($currUrl, ['realestates.php','realestate_insert.php','realestate_update.php']) ? 'active':''  ?>'><a href="realestates.php?stype=0">Property</a></li>                            
                            <li class='<?= $currUrl == in_array($currUrl, ['reservedproperty.php']) ? 'active':''  ?>'><a href="<?= $reservedRentUrl ?>">Reserved Property</a></li>
                        </ul>
                    </div>
                </li>
                <li class="">
                    <div class="dropdown">
                        <span class="dropdown-toggle" type="button" data-toggle="dropdown">For Sale
                        <span class="caret"></span></span>
                        <ul class="dropdown-menu">                            
                            <li class='<?= $currUrl == in_array($currUrl, ['realestates.php','realestate_insert.php','realestate_update.php']) ? 'active':''  ?>'><a href="realestates.php?stype=1">Property</a></li>
                            <li class='<?= $currUrl == in_array($currUrl, ['reservedproperty.php']) ? 'active':''  ?>'><a href="<?= $reservedSaleUrl ?>">Reserved Property</a></li>
                            <li class='<?= $currUrl == in_array($currUrl, ['public-submission-approval.php']) ? 'active':''  ?>'><a href="public-submission-approval">Public Submission Approval</a></li>
                        </ul>
                    </div>
                </li>                
                <li class="">
                    <div class="dropdown">
                        <span class="dropdown-toggle" type="button" data-toggle="dropdown">Auction
                        <span class="caret"></span></span>
                        <ul class="dropdown-menu">                            
                            <li class='<?= $currUrl == in_array($currUrl, ['auction.php','auction_insert.php','auction_update.php']) ? 'active':''  ?>'><a href="auction.php">Auction</a></li>
                            <li class='<?= $currUrl == in_array($currUrl, ['bid_made.php','bid.php','bid_insert.php']) ? 'active':''  ?>'><a href="bid_made.php">Bids Made</a></li>
                            <li class='<?= $currUrl == in_array($currUrl, ['registered_bidders.php']) ? 'active':''  ?>'><a href="registered_bidders.php">Registered Bidders</a></li>
                        </ul>
                    </div>
                </li>
                <li class='<?= $currUrl == in_array($currUrl, ['bank.php','bank_insert.php','bank_update.php']) ? 'active':''  ?>'><a href="bank.php">Banks</a></li>
                <li class='<?= $currUrl == in_array($currUrl, ['agents.php','agent_insert.php','agent_update.php']) ? 'active':''  ?>'><a href="agents.php">Agents</a></li>
                <li class='<?= $currUrl == in_array($currUrl, ['lawyers.php','lawyer_insert.php','lawyer_update.php']) ? 'active':''  ?>'><a href="lawyers.php">Lawyers</a></li>                
                
                <li class='<?= in_array($currUrl, ['admin_access.php','access_user_update.php','access_user_insert.php']) ? 'active':''  ?>'><a href="admin_access.php">Admin Access</a></li> 
                <li class='<?= $currUrl == 'users.php' ? 'active':''  ?>'><a href="users.php">Users</a></li>
                <li class="">
                    <div class="dropdown">
                        <span class="dropdown-toggle" type="button" data-toggle="dropdown">Setting
                        <span class="caret"></span></span>
                        <ul class="dropdown-menu">                                     
                            <li class='<?= $currUrl == in_array($currUrl, ['banner_list.php','banner_insert.php','banner_update.php']) ? 'active':''  ?>'><a href="banner_list.php">Paid Advertisement Banner</a></li>
                            <li class='<?= $currUrl == in_array($currUrl, ['country.php','country_insert.php','country_update.php']) ? 'active':''  ?>'><a href="country.php">Country</a></li>
                            <li class='<?= $currUrl == in_array($currUrl, ['propertytypes.php','propertytype_insert.php','propertytype_update.php']) ? 'active':''  ?>'><a href="propertytypes.php">Property Type</a></li>
							<li class='<?= $currUrl == in_array($currUrl, ['realestates.php','realestate_insert.php','realestate_update.php']) ? 'active':''  ?>'><a href="realestates.php?stype=2">Sold Property</a></li>
						</ul>
                    </div>
                </li>
                <li ><a href="index.php">Logout</a></li>
            </ul>
        </div><!--/.nav-collapse -->
        <?php if (isset($_SESSION['message'])) {  ?>
        <div class="alert alert-success fade in alert-dismissible" style="margin-top:18px;">
            <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">x</a>
            <strong>Success!</strong> <?php echo $_SESSION['message'];  ?>
        </div>
        <?php unset($_SESSION['message']);  } ?>
        <?php if (isset($_SESSION['error'])) {  ?>
        <div class="alert alert-danger fade in alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">x</a>
            <strong>Error!</strong> <?php echo $_SESSION['error'];  ?>
        </div>
        <?php unset($_SESSION['error']);  } ?>
    </div>
</div>