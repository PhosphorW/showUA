<?php
/**
 * WordPress显示访客UA信息：Show UserAgent精简版 By Phower
 */
function show_ua_scripts() {
	wp_enqueue_style( 'ua_scripts', get_template_directory_uri() . '/show-useragent/ua-style.css' );
}

add_action( 'wp_enqueue_scripts', 'show_ua_scripts' );


/* 显示国家 */
function CID_get_country( $ip ) {
	require_once( dirname( __FILE__ ) . '/ip2c/ip2c.php' );
	if ( isset( $GLOBALS['ip2c'] ) ) {
		global $ip2c;
	} else {
		$ip2c            = new ip2country( dirname( __FILE__ ) . '/ip2c/ip-to-country.bin' );
		$GLOBALS['ip2c'] = $ip2c;
	}

	return $ip2c->get_country( $ip );
}

function CID_get_flag( $ip ) {
	if ( $ip == '127.0.0.1' ) {
		$code = 'wordpress';
		$name = 'Localhost';
	} else {
		$country = CID_get_country( $ip );
		if ( ! $country ) {
			return "";
		}

		$code = strtolower( $country['id2'] );
		$name = $country['name'];
	}
	if ( $name == 'China' ) {
		$name = '来自天朝的朋友';
	}
	if ( $name == 'United States' ) {
		$name = '这家伙可能用了美佬的代理';
	}
	if ( $name == 'Reserved' ) {
		$name = '来自火星？？？';
	}
	if ( $name == 'Japan' ) {
		$name = '这家伙可能用了岛国的代理';
	}
	$output = stripslashes( '<span class="country-flag"><img src="%IMAGE_BASE%/%COUNTRY_CODE%.png" title="%COUNTRY_NAME%" alt="%COUNTRY_NAME%" /></span>' );

	if ( ! $output ) {
		return "";
	}

	$output = str_replace( "%COUNTRY_CODE%", $code, $output );
	$output = str_replace( "%COUNTRY_NAME%", $name, $output );
	$output = str_replace( "%COMMENTER_IP%", $ip, $output );
	$output = str_replace( "%IMAGE_BASE%", get_stylesheet_directory_uri() . '/show-useragent/flags', $output );

	return $output;
}

function CID_print_comment_flag() {
	$ip = get_comment_author_IP();
	echo CID_get_flag( $ip );
}

/* 浏览器 */

function CID_windows_detect_os( $ua ) {
	$os_name   = $os_code = $os_ver = null;
	$os_before = null;

	if ( preg_match( '/Windows 95/i', $ua ) || preg_match( '/Win95/', $ua ) ) {
		$os_name = "Windows";
		$os_code = "windows";
		$os_ver  = "95";
	} elseif ( preg_match( '/Windows NT 5.0/i', $ua ) || preg_match( '/Windows 2000/i', $ua ) ) {
		$os_name = "Windows";
		$os_code = "windows";
		$os_ver  = "2000";
	} elseif ( preg_match( '/Win 9x 4.90/i', $ua ) || preg_match( '/Windows ME/i', $ua ) ) {
		$os_name = "Windows";
		$os_code = "windows";
		$os_ver  = "ME";
	} elseif ( preg_match( '/Windows.98/i', $ua ) || preg_match( '/Win98/i', $ua ) ) {
		$os_name = "Windows";
		$os_code = "windows";
		$os_ver  = "98";
	} elseif ( preg_match( '/Windows NT 6.0/i', $ua ) ) {
		$os_name = "Windows";
		$os_code = "windows_vista";
		$os_ver  = "Vista";
	} elseif ( preg_match( '/Windows NT 6.1/i', $ua ) ) {
		$os_name = "Windows";
		$os_code = "windows_win7";
		$os_ver  = "7";
	} elseif ( preg_match( '/Windows NT 6.2/i', $ua ) ) {
		$os_name = "Windows";
		$os_code = "windows_win8";
		$os_ver  = "8";
	} elseif ( preg_match( '/Windows NT 6.3/i', $ua ) ) {
		$os_name = "Windows";
		$os_code = "windows_win8";
		$os_ver  = "8.1";
	} elseif ( preg_match( '/Windows NT 6.4/i', $ua ) ) {
		$os_name = "Windows";
		$os_code = "windows_win8";
		$os_ver  = "10";
	} elseif ( preg_match( '/Windows NT 10.0/i', $ua ) ) {
		$os_name = "Windows";
		$os_code = "windows_win8";
		$os_ver  = "10";
	} elseif ( preg_match( '/Windows NT 5.1/i', $ua ) ) {
		$os_name = "Windows";
		$os_code = "windows";
		$os_ver  = "XP";
	} elseif ( preg_match( '/Windows NT 5.2/i', $ua ) ) {
		$os_name = "Windows";
		$os_code = "windows";
		if ( preg_match( '/Win64/i', $ua ) ) {
			$os_ver = "XP 64 bit";
		} else {
			$os_ver = "Server 2003";
		}
	} elseif ( preg_match( '/Mac_PowerPC/i', $ua ) ) {
		$os_name = "Mac OS";
		$os_code = "macos";
	} elseif ( preg_match( '/Windows Phone/i', $ua ) ) {
		$matches = explode( ';', $ua );
		$os_name = $matches[2];
		$os_code = "windows_phone7";
	} elseif ( preg_match( '/Windows NT 4.0/i', $ua ) || preg_match( '/WinNT4.0/i', $ua ) ) {
		$os_name = "Windows";
		$os_code = "windows";
		$os_ver  = "NT 4.0";
	} elseif ( preg_match( '/Windows NT/i', $ua ) || preg_match( '/WinNT/i', $ua ) ) {
		$os_name = "Windows";
		$os_code = "windows";
		$os_ver  = "NT";
	} else {
		$os_name = '未知系统';
		$os_code = 'other';
	}

	$os_before = '<span class="os os_win"><i class="fa fa-windows"></i>';

	return array( $os_name, $os_code, $os_ver, $os_before );
}

function CID_unix_detect_os( $ua ) {
	$os_name   = $os_ver = $os_code = null;
	$os_before = null;

	if ( preg_match( '/Linux/i', $ua ) ) {
		$os_name   = "Linux";
		$os_code   = "linux";
		$os_before = '<span class="os os_linux"><i class="fa fa-linux"></i>';
		if ( preg_match( '#Debian#i', $ua ) ) {
			$os_code = "debian";
			$os_name = "Debian GNU/Linux";
		} elseif ( preg_match( '#Mandrake#i', $ua ) ) {
			$os_code = "mandrake";
			$os_name = "Mandrake Linux";
		} elseif ( preg_match( '#Android#i', $ua ) ) {//Android
			$matches   = explode( ';', $ua );
			$os_code   = "android";
			$matches2  = explode( ')', $matches[4] );
			$os_name   = $matches[1];
			if  ( strpos($os_name, 'Android') == false){
			$os_name = $matches[2];
			};
			$os_before = '<span class="os os_android"><i class="fa fa-android"></i>';
		} elseif ( preg_match( '#SuSE#i', $ua ) ) {
			$os_code = "suse";
			$os_name = "SuSE Linux";
		} elseif ( preg_match( '#Novell#i', $ua ) ) {
			$os_code = "novell";
			$os_name = "Novell Linux";
		} elseif ( preg_match( '#Ubuntu#i', $ua ) ) {
			$os_code = "ubuntu";
			$os_name = "Ubuntu Linux";
		} elseif ( preg_match( '#Red ?Hat#i', $ua ) ) {
			$os_code = "redhat";
			$os_name = "RedHat Linux";
		} elseif ( preg_match( '#Gentoo#i', $ua ) ) {
			$os_code = "gentoo";
			$os_name = "Gentoo Linux";
		} elseif ( preg_match( '#Fedora#i', $ua ) ) {
			$os_code = "fedora";
			$os_name = "Fedora Linux";
		} elseif ( preg_match( '#MEPIS#i', $ua ) ) {
			$os_name = "MEPIS Linux";
		} elseif ( preg_match( '#Knoppix#i', $ua ) ) {
			$os_name = "Knoppix Linux";
		} elseif ( preg_match( '#Slackware#i', $ua ) ) {
			$os_code = "slackware";
			$os_name = "Slackware Linux";
		} elseif ( preg_match( '#Xandros#i', $ua ) ) {
			$os_name = "Xandros Linux";
		} elseif ( preg_match( '#Kanotix#i', $ua ) ) {
			$os_name = "Kanotix Linux";
		}

	} elseif ( preg_match( '/FreeBSD/i', $ua ) ) {
		$os_name   = "FreeBSD";
		$os_code   = "freebsd";
		$os_before = '<span class="os os_unix"><i class="fa fa-desktop"></i>';
	} elseif ( preg_match( '/NetBSD/i', $ua ) ) {
		$os_name   = "NetBSD";
		$os_code   = "netbsd";
		$os_before = '<span class="os os_unix"><i class="fa fa-desktop"></i>';
	} elseif ( preg_match( '/OpenBSD/i', $ua ) ) {
		$os_name   = "OpenBSD";
		$os_code   = "openbsd";
		$os_before = '<span class="os os_unix"><i class="fa fa-desktop"></i>';
	} elseif ( preg_match( '/IRIX/i', $ua ) ) {
		$os_name   = "SGI IRIX";
		$os_code   = "sgi";
		$os_before = '<span class="os os_unix"><i class="fa fa-desktop"></i>';
	} elseif ( preg_match( '/SunOS/i', $ua ) ) {
		$os_name   = "Solaris";
		$os_code   = "sun";
		$os_before = '<span class="os os_unix"><i class="fa fa-desktop"></i>';
	} elseif ( preg_match( '#iPod.*.CPU.([a-zA-Z0-9.( _)]+)#i', $ua, $matches ) ) {
		$os_name   = "iPod";
		$os_code   = "iphone";
		$os_ver    = $matches[1];
		$os_before = '<span class="os os_mac"><i class="fa fa-apple"></i>';
	} elseif ( preg_match( '#iPhone.*.CPU.([a-zA-Z0-9.( _)]+)#i', $ua, $matches ) ) {
		$os_name   = "iPhone";
		$os_code   = "iphone";
		$os_ver    = $matches[1];
		$os_before = '<span class="os os_mac"><i class="fa fa-apple"></i>';
	} elseif ( preg_match( '#iPad.*.CPU.([a-zA-Z0-9.( _)]+)#i', $ua, $matches ) ) {
		$os_name   = "iPad";
		$os_code   = "ipad";
		$os_ver    = $matches[1];
		$os_before = '<span class="os os_mac"><i class="fa fa-apple"></i>';
	} elseif ( preg_match( '/Mac OS X.([0-9. _]+)/i', $ua, $matches ) ) {
		$os_name = "Mac OS";
		$os_code = "macos";
		if ( count( explode( 7, $matches[1] ) ) > 1 ) {
			$matches[1] = 'Lion ' . $matches[1];
		} elseif ( count( explode( 8, $matches[1] ) ) > 1 ) {
			$matches[1] = 'Mountain Lion ' . $matches[1];
		}
		$os_ver    = "X " . $matches[1];
		$os_before = '<span class="os os_mac"><i class="fa fa-apple"></i>';
	} elseif ( preg_match( '/Macintosh/i', $ua ) ) {
		$os_name   = "Mac OS";
		$os_code   = "macos";
		$os_before = '<span class="os os_mac"><i class="fa fa-apple"></i>';
	} elseif ( preg_match( '/Unix/i', $ua ) ) {
		$os_name   = "UNIX";
		$os_code   = "unix";
		$os_before = '<span class="os os_unix"><i class="fa fa-desktop"></i>';
	} elseif ( preg_match( '/CrOS/i', $ua ) ) {
		$os_name   = "Google Chrome OS";
		$os_code   = "chromeos";
		$os_before = '<span class="os os_android"><i class="fa fa-android"></i>';
	} elseif ( preg_match( '/Fedor.([0-9. _]+)/i', $ua, $matches ) ) {
		$os_name   = "Fedora";
		$os_code   = "fedora";
		$os_ver    = $matches[1];
		$os_before = '<span class="os os_linux"><i class="fa fa-linux"></i>';
	} else {
		$os_name   = 'Unknow Os';
		$os_code   = 'other';
		$os_before = '<span class="os os_other"><i class="fa fa-desktop"></i>';
	}

	return array( $os_name, $os_code, $os_ver, $os_before );
}


function CID_detect_browser( $ua ) {
	$browser_name   = $browser_code = $browser_ver = $os_name = $os_code = $os_ver = null;
	$browser_before = null;
	$os_before      = null;
	$ua             = preg_replace( "/FunWebProducts/i", "", $ua );
	if ( preg_match( '#(Firefox|Phoenix|Firebird|BonEcho|GranParadiso|Minefield|Iceweasel)/4([a-zA-Z0-9.]+)#i', $ua, $matches ) ) {
		$browser_name   = 'Firefox';
		$browser_code   = 'firefox';
		$browser_ver    = '4' . $matches[2];
		$browser_before = '<span class="ua ua_firefox"><i class="fa fa-firefox"></i>';
		if ( preg_match( '/Windows/i', $ua ) ) {
			list( $os_name, $os_code, $os_ver, $os_before ) = CID_windows_detect_os( $ua );
		} else {
			list( $os_name, $os_code, $os_ver, $os_before ) = CID_unix_detect_os( $ua );
		}
	} elseif ( preg_match( '#(Firefox|Phoenix|Firebird|BonEcho|GranParadiso|Minefield|Iceweasel)/([a-zA-Z0-9.]+)#i', $ua, $matches ) ) {
		$browser_name   = 'Firefox';
		$browser_code   = 'firefox';
		$browser_ver    = $matches[2];
		$browser_before = '<span class="ua ua_firefox"><i class="fa fa-firefox"></i>';
		if ( preg_match( '/Windows/i', $ua ) ) {
			list( $os_name, $os_code, $os_ver, $os_before ) = CID_windows_detect_os( $ua );
		} else {
			list( $os_name, $os_code, $os_ver, $os_before ) = CID_unix_detect_os( $ua );
		}
	} elseif ( preg_match( '#SE 2([a-zA-Z0-9.]+)#i', $ua, $matches ) ) {
		$browser_name   = '搜狗浏览器';
		$browser_code   = 'sogou';
		$browser_ver    = '2' . $matches[1];
		$browser_before = '<span class="ua ua_sogou"><i class="fa fa-globe"></i>';
		if ( preg_match( '/Windows/i', $ua ) ) {
			list( $os_name, $os_code, $os_ver, $os_before ) = CID_windows_detect_os( $ua );
		} else {
			list( $os_name, $os_code, $os_ver, $os_before ) = CID_unix_detect_os( $ua );
		}
	} elseif ( preg_match( '#baidubrowser ([a-zA-Z0-9.]+)#i', $ua, $matches ) ) {
		$browser_name   = '百度浏览器';
		$browser_code   = 'baidubrowser';
		$browser_ver    = $matches[1];
		$browser_before = '<span class="ua ua_ucweb"><i class="fa fa-globe"></i>';
		if ( preg_match( '/Windows/i', $ua ) ) {
			list( $os_name, $os_code, $os_ver, $os_before ) = CID_windows_detect_os( $ua );
		} else {
			list( $os_name, $os_code, $os_ver, $os_before ) = CID_unix_detect_os( $ua );
		}
	} elseif ( preg_match( '#360([a-zA-Z0-9.]+)#i', $ua, $matches ) ) {
		$browser_name   = '360浏览器';
		$browser_code   = '360se';
		$browser_ver    = $matches[1];
		$browser_before = '<span class="ua ua_ucweb"><i class="fa fa-globe"></i>';
		if ( preg_match( '/Windows/i', $ua ) ) {
			list( $os_name, $os_code, $os_ver, $os_before ) = CID_windows_detect_os( $ua );
		} else {
			list( $os_name, $os_code, $os_ver, $os_before ) = CID_unix_detect_os( $ua );
		}
	} elseif ( preg_match( '#QQBrowser/([a-zA-Z0-9.]+)#i', $ua, $matches ) ) {
		$browser_name   = 'QQ浏览器';
		$browser_code   = 'qqbrowser';
		$browser_ver    = $matches[1];
		$browser_before = '<span class="ua ua_qq"><i class="fa fa-globe"></i>';
		if ( preg_match( '/Windows/i', $ua ) ) {
			list( $os_name, $os_code, $os_ver, $os_before ) = CID_windows_detect_os( $ua );
		} else {
			list( $os_name, $os_code, $os_ver, $os_before ) = CID_unix_detect_os( $ua );
		}
	} elseif ( preg_match( '#Chrome/([a-zA-Z0-9.]+)#i', $ua, $matches ) ) {
		$browser_name   = 'Chrome';
		$browser_code   = 'chrome';
		$browser_ver    = $matches[1];
		$browser_before = '<span class="ua ua_chrome"><i class="fa fa-chrome"></i>';
		if ( preg_match( '/Windows/i', $ua ) ) {
			list( $os_name, $os_code, $os_ver, $os_before ) = CID_windows_detect_os( $ua );
		} else {
			list( $os_name, $os_code, $os_ver, $os_before ) = CID_unix_detect_os( $ua );
		}
	} elseif ( preg_match( '#Arora/([a-zA-Z0-9.]+)#i', $ua, $matches ) ) {
		$browser_name   = 'Arora';
		$browser_code   = 'arora';
		$browser_ver    = $matches[1];
		$browser_before = '<span class="ua ua_other"><i class="fa fa-globe"></i>';
		if ( preg_match( '/Windows/i', $ua ) ) {
			list( $os_name, $os_code, $os_ver, $os_before ) = CID_windows_detect_os( $ua );
		} else {
			list( $os_name, $os_code, $os_ver, $os_before ) = CID_unix_detect_os( $ua );
		}
	} elseif ( preg_match( '#Maxthon( |\/)([a-zA-Z0-9.]+)#i', $ua, $matches ) ) {
		$browser_name   = '傲游浏览器';
		$browser_code   = 'maxthon';
		$browser_ver    = $matches[2];
		$browser_before = '<span class="ua ua_other"><i class="fa fa-globe"></i>';
		if ( preg_match( '/Win/i', $ua ) ) {
			list( $os_name, $os_code, $os_ver, $os_before ) = CID_windows_detect_os( $ua );
		} else {
			list( $os_name, $os_code, $os_ver, $os_before ) = CID_unix_detect_os( $ua );
		}
	} elseif ( preg_match( '#CriOS/([a-zA-Z0-9.]+)#i', $ua, $matches ) ) {
		$browser_name   = 'Chrome #iOS';
		$browser_code   = 'crios';
		$browser_ver    = $matches[1];
		$browser_before = '<span class="ua ua_chrome"><i class="fa fa-chrome"></i>';
		if ( preg_match( '/Windows/i', $ua ) ) {
			list( $os_name, $os_code, $os_ver, $os_before ) = CID_windows_detect_os( $ua );
		} else {
			list( $os_name, $os_code, $os_ver, $os_before ) = CID_unix_detect_os( $ua );
		}
	} elseif ( preg_match( '#Safari/([a-zA-Z0-9.]+)#i', $ua, $matches ) ) {
		$browser_name   = 'Safari';
		$browser_code   = 'safari';
		$browser_ver    = $matches[1];
		$browser_before = '<span class="ua ua_apple"><i class="fa fa-safari"></i>';
		if ( preg_match( '/Windows/i', $ua ) ) {
			list( $os_name, $os_code, $os_ver, $os_before ) = CID_windows_detect_os( $ua );
		} else {
			list( $os_name, $os_code, $os_ver, $os_before ) = CID_unix_detect_os( $ua );
		}
	} elseif ( preg_match( '#opera mini#i', $ua ) ) {
		$browser_name = 'Opera Mini';
		$browser_code = 'opera';
		preg_match( '#Opera/([a-zA-Z0-9.]+)#i', $ua, $matches );
		$browser_ver    = $matches[1];
		$browser_before = '<span class="ua ua_opera"><i class="fa fa-opera"></i>';
		if ( preg_match( '/Windows/i', $ua ) ) {
			list( $os_name, $os_code, $os_ver, $os_before ) = CID_windows_detect_os( $ua );
		} else {
			list( $os_name, $os_code, $os_ver, $os_before ) = CID_unix_detect_os( $ua );
		}
	} elseif ( preg_match( '#Opera.(.*)Version[ /]([a-zA-Z0-9.]+)#i', $ua, $matches ) ) {
		$browser_name   = 'Opera';
		$browser_code   = 'opera';
		$browser_ver    = $matches[2];
		$browser_before = '<span class="ua ua_opera"><i class="fa fa-opera"></i>';
		if ( preg_match( '/Windows/i', $ua ) ) {
			list( $os_name, $os_code, $os_ver, $os_before ) = CID_windows_detect_os( $ua );
		} else {
			list( $os_name, $os_code, $os_ver, $os_before ) = CID_unix_detect_os( $ua );
		}
		if ( ! $os_name ) {
			list( $os_name, $os_code, $os_ver, $os_before ) = CID_unix_detect_os( $ua );
		}
	} elseif ( preg_match( '#Opera/([a-zA-Z0-9.]+)#i', $ua, $matches ) ) {
		$browser_name   = 'Opera Mini';
		$browser_code   = 'opera';
		$browser_ver    = $matches[1];
		$browser_before = '<span class="ua ua_opera"><i class="fa fa-opera"></i>';
		if ( preg_match( '/Windows/i', $ua ) ) {
			list( $os_name, $os_code, $os_ver, $os_before ) = CID_windows_detect_os( $ua );
		} else {
			list( $os_name, $os_code, $os_ver, $os_before ) = CID_unix_detect_os( $ua );
		}
	} elseif ( preg_match( '#UCWEB([a-zA-Z0-9.]+)#i', $ua, $matches ) ) {
		$browser_name   = 'UC';
		$browser_code   = 'ucweb';
		$browser_ver    = $matches[1];
		$browser_before = '<span class="ua ua_ucweb"><i class="fa fa-globe"></i>';
		if ( preg_match( '/Windows/i', $ua ) ) {
			list( $os_name, $os_code, $os_ver, $os_before ) = CID_windows_detect_os( $ua );
		} else {
			list( $os_name, $os_code, $os_ver, $os_before ) = CID_unix_detect_os( $ua );
		}
	} elseif ( preg_match( '#MSIE ([a-zA-Z0-9.]+)#i', $ua, $matches ) ) {
		$browser_name   = 'Internet Explorer';
		$browser_ver    = $matches[1];
		$browser_before = '<span class="ua ua_ie"><i class="fa fa-internet-explorer"></i>';
		if ( strpos( $browser_ver, '7' ) !== false || strpos( $browser_ver, '8' ) !== false ) {
			$browser_code = 'ie8';
		} elseif ( strpos( $browser_ver, '9' ) !== false ) {
			$browser_code = 'ie9';
		} elseif ( strpos( $browser_ver, '10' ) !== false ) {
			$browser_code = 'ie10';
		} else {
			$browser_code = 'ie';
		}
		list( $os_name, $os_code, $os_ver, $os_before ) = CID_windows_detect_os( $ua );
	} elseif ( preg_match( '#^Mozilla/5.0#i', $ua ) && preg_match( '#rv:([a-zA-Z0-9.]+)#i', $ua, $matches ) ) {
		$browser_name   = 'Firefox 5.0';
		$browser_code   = 'mozilla';
		$browser_ver    = $matches[1];
		$browser_before = '<span class="ua ua_firefox"><i class="fa fa-firefox"></i>';
		if ( preg_match( '/Windows/i', $ua ) ) {
			list( $os_name, $os_code, $os_ver, $os_before ) = CID_windows_detect_os( $ua );
		} else {
			list( $os_name, $os_code, $os_ver, $os_before ) = CID_unix_detect_os( $ua );
		}
	} else {
		$browser_name   = '未知浏览器';
		$browser_code   = 'null';
		$browser_before = '<span class="ua ua_other"><i class="fa fa-globe"></i>';
	}

	if ( ! $os_name ) {
		$os_name = 'Unknown OS';
		$os_code = 'other';
		$os_before = '<span class="os os_other"><i class="fa fa-desktop"></i>';
	}

	return array(
		$browser_name,
		$browser_code,
		$browser_ver,
		$browser_before,
		$os_name,
		$os_code,
		$os_ver,
		$os_before,
	);
}


function CID_friendly_string_without_template( $browser_name = '', $browser_code = '', $browser_ver = '', $browser_before = '', $os_name = '', $os_code = '', $os_ver = '', $os_before = '' ) {
	$browser_name = htmlspecialchars( $browser_name );
	$browser_code = htmlspecialchars( $browser_code );
	$browser_ver  = htmlspecialchars( $browser_ver );
	$os_name      = htmlspecialchars( $os_name );
	$os_code      = htmlspecialchars( $os_code );
	$os_ver       = htmlspecialchars( $os_ver );

	$text1 = '';
	$text2 = '';

	if ( $browser_name && $os_name ) {
		$text1 = "$browser_name | $browser_ver ";
		$text2 = "$os_name";
		if  ( strpos($text1, '未知浏览器') !== false){
			$text1 = "$browser_name";
			};
	} elseif ( $browser_name ) {
		$text1 = "$browser_name | $browser_ver";
		if  ( strpos($text1, '未知浏览器') !== false){
			$text1 = "$browser_name";
			};
	} elseif ( $os_name ) {
		$text1 = "$os_name | $os_ver";
	}
	return $browser_before . ' ' . $text1 . ' </span>' . $os_before . ' ' . $text2 . '</span>';
}

function CID_get_comment_browser_without_template() {
	global $comment;
	if ( ! $comment->comment_agent ) {
		return;
	}
	list ( $browser_name, $browser_code, $browser_ver, $browser_before, $os_name, $os_code, $os_ver, $os_before ) = CID_detect_browser( $comment->comment_agent );
	$string = CID_friendly_string_without_template( $browser_name, $browser_code, $browser_ver, $browser_before, $os_name, $os_code, $os_ver, $os_before );

	return $string;
}

function CID_print_comment_browser() {
	echo CID_get_comment_browser_without_template();
}

?>
