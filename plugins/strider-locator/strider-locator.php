<?php
/**
 * Plugin Name: Strider Locator
 * Plugin URI:
 * Version: 1.0.0
 * Author: Strider Sports Intl., Inc.
 * Description: Provides a simple single-page dealer locator
 * Text Domain: strider-locator
 * License: BSD 3-Clause
 *
 * Copyright (c) 2016-2017, Strider Sports Intl., Inc.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the
 * following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the
 * following disclaimer in the documentation and/or other materials provided with the distribution.
 *
 * 3. Neither the name of Strider Sports Intl., Inc., nor the names of its contributors may be used to endorse or
 * promote products derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */


/*
 * Require PHP version 5.2 or greater
 */
function StriderLoc_VCheck() {
    if (version_compare(phpversion(), '5.2') < 0) {
        add_action('admin_notices', 'StriderLoc_VNotice');
        return (false);
    }

    return (true);
}

function StriderLoc_VNotice() {
    $notice = <<<EOM
<div class="updated fade">
  Error: plugin "Strider Locator" requires a newer version of PHP.<br>
  Minimum PHP Version: 5.2<br>
  Current PHP Version: %s
</div>
EOM;

    $d_notice = sprintf($notice, phpversion());
    echo $d_notice;
}

if (StriderLoc_VCheck()) {
    require_once(plugin_dir_path(__FILE__) . 'templates.php');
    require_once(plugin_dir_path(__FILE__) . 'admin.php');
    require_once(plugin_dir_path(__FILE__) . 'frontend.php');
    require_once(plugin_dir_path(__FILE__) . 'init.php');

    $se = new StriderLoc();
    $se->init();
}