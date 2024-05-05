<header class="header clearfix">
    <button type="button" id="toggleMenu" class="toggle_menu">
        <i class='uil uil-bars'></i>
    </button>
    <button id="collapse_menu" class="collapse_menu">
        <i class="uil uil-bars collapse_menu--icon "></i>
        <span class="collapse_menu--label"></span>
    </button>
    <div style="margin-left:10px;">
        <a href="index.php" style="text-align:center;color:black">
            <img src="icon/logo_small.jpg" style="width:25px;height:25px;border-radius:50px;" alt="">
            <br>
            <span style="font-size:12px; font-weight:bold;">Billion Empress</span>
        </a>
        
    </div>
    <div class="header_right">
        <ul>
            <li>
                <a href="create_order.php" class="upload_btn" title="Create New Course">Create New Order</a>
            </li>
            <li>
                <a href="orders.php?received=0" class="option_links" title="My Order"><i class='uil uil-shopping-cart-alt'></i></a>
            </li>
            <li>
                <a href="orders.php?received=1" class="option_links" title="New Order"><i class='uil uil-bell'></i></a>
            </li>

            <li class="ui dropdown">
                <a href="#" class="opts_account" title="Account">
                    <img src="uploads/profiles/<?php echo $user['profile_image'] ?>" alt="" style="width:35px; height:35px;">
                </a>
                <div class="menu dropdown_account">
                    <div class="channel_my">
                        <div class="profile_link">
                            <img src="uploads/profiles/<?php echo $user['profile_image'] ?>" alt="" style="width:40px; height:40px;">
                            <div class="pd_content">
                                <div class="rhte85">
                                    <h6><?php echo $user['name']; ?></h6>
                                    <div class="mef78" title="Verify">
                                        <i class='uil uil-check-circle'></i>
                                    </div>
                                </div>
                                <span><?php echo $user['email'];?></span>
                            </div>							
                        </div>
                        <a href="my_profile.php" class="dp_link_12">View Profile</a>						
                    </div>
                    <div class="night_mode_switch__btn">
                        <a href="#" id="night-mode" class="btn-night-mode">
                            <i class="uil uil-moon"></i> Night mode
                            <span class="btn-night-mode-switch">
                                <span class="uk-switch-button"></span>
                            </span>
                        </a>
                    </div>
                    <a href="my_business.php?is_sold_out=0" class="item channel_item">My Business</a>						
                    <a href="create_order.php" class="item channel_item">Create Order</a>
                    <a href="create_invoice.php" class="item channel_item">Voucher</a>
                    <a href="product_left.php" class="item channel_item">Product Left</a>
                    <a href="my_group.php" class="item channel_item">My Groups</a>
                    <a href="sign_in.html" class="item channel_item">My Leader's Groups</a>
                    <a href="logout.php" class="item channel_item">Logout</a>
                </div>
            </li>
        </ul>
    </div>
</header>