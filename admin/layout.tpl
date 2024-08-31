<!DOCTYPE html>
<html lang="{{ lang.getLocale}}">
	<head>
		{% HOOK.adminHead %}
		<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=5">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<title>299ko - {{ Lang.core-administration }}</title>
		<base href="{{ SHOW.siteUrl }}/">
		<link rel="icon" href=" data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABb2lDQ1BpY2MAACiRdZE7SwNBFIU/EyXigxRaiCik8FUYCApiqbFIE0Sigq8mu9lNhCQuuxsk2Ao2FgEL0cZX4T/QVrBVEARFELHyB/hqJKx3EiEicZbZ+3FmzmXmDPjiWT3nNEYgl3ftRCwaWlhcCgVeaKGXAIMEk7pjTc7MxPl3fN7RoOptWPX6f1/d0ZoyHB0amoXHdMt2hSeE4+uupXhbuFPPJFPCh8LDthxQ+ErpWpWfFaer/K7YnktMgU/1DKV/sfaL9YydEx4S7stlC/rPedRN2oz8/KzUbpk9OCSIESWERoFVsriEpeYls/q+SMU3zZp4dPlbFLHFkSYj3mFRC9LVkGqKbsiXpahy/5unY46OVLu3RaHpyfPe+iGwA+WS530deV75GPyPcJGv+dckp/EP0Us1re8AgptwdlnTtF0434KuBytpJyuSX6bPNOH1FNoXoeMGWparWf2sc3IPcxvyRNewtw8Dsj+48g3mO2f+n7tX+AAAAAlwSFlzAAAOwwAADsMBx2+oZAAAAi1JREFUOMtjZEADN2/dZX3/4WPX06fPo4BcASBm+s/A8FFMRHgzGztbloWZ0XcGfODb9+8Ca9ZteWFg6vpfQ8/uvyYQ6xo5/V+8bO3nT58+S6KrZ0IXuHr1xmdzM6MeX2/Xb+/efWB48/Y9g6uL/U87G/Oes+cuvUVXz4IuYGpi+Pf///9zpKUk8llZWbj+/v3HIC0l/llOVnqmvJzML4IugALGf0BTYJx//8BMRmwKcRlg8P37d76///4xgMz59u07F1DMiIEQACl+9vxlzqSpc98Zmrn9l1Iw+i+taAQOxO6+6Z8ePHxcBVTDhNOAN2/fhdU39fyXkDf4Lylv+F9W2QSMpRQM/4vJ6v0vrWz+f/fewwycgXjg4LHKFas2fGVmYmIAYg6gzz8BPX6NhZn5HhMjk/uatVv+a2upVwCVzsBqwJmzl/RfPHmeISwidJThPwMfMAReCTAwPbxw99QfOWWTSe/evf959erNFJwuACYUhs8fPl3++OHuVSw+vA/EvJ+/fPmIMxZ4ebkZuXi5lbCFDxsj45ovnz6f4uLklMBpgL6+9g05Rbl0kHp0A+7cOf1YXkk+UFNT9T3OWHj1+k14XWP3P3Fp3blAM6SQpAT4RdXrSsqb/t++cy8LZxiIiYqsBKYDCT4+no4duw74PHv6/Agw3n9LSkqYOzvZSAUFeDWpKCvORNaDNXkCNZlcuHQ1+uKla17//v5j1dZW32lmYrCEkZHxKLpaAM7P7FOQafyUAAAAAElFTkSuQmCC">
		{{ show.linkTags }}
		<link rel="stylesheet" href="{{ADMIN_URL}}styles.css" media="all">
		{{ show.scriptTags }}
		<script type="text/javascript" src="{{ADMIN_URL}}scripts.js"></script>
		{% HOOK.endAdminHead %}
	</head>
	<body>
		<div id="container">
			<aside id="header">
				<nav id="adminNav">
					<label id="labelBurger" for="burger">
						<i class="fa-solid fa-bars"></i>
					</label>
					<input type="checkbox" id="burger"/>
					<div class="main_nav" role="navigation" aria-label="{{ Lang.core-nav-admin }}">
						<ul id="navigation">
							<li id="nav-change-container">
								<a role="button" href="#" onclick="return changeMainNav()">
									<i title="{{ Lang.admin-change-sidebar-width }}" id="nav-icon-grow"></i>
								</a>
							</li>
							<li class="site">
								<a target="_blank" href="{{SITE_URL}}">
									<i title="{{ Lang.core-goto-site }}" class="fa-regular fa-eye"></i>
									<span>{{ Lang.core-goto-site }}</span>
								</a>
							</li>
							{{ show.adminNavigation }}
							<li class="site">
								<a href="{{ROUTER.generate("logout")}}">
									<i title="{{ Lang.core-disconnection }}" class="fa-solid fa-arrow-right-from-bracket"></i>
									<span>{{ Lang.core-disconnection }}</span>
								</a>
							</li>
							<li class="just_using">
								<a target="_blank" href="https://299ko.ovh">
									<i title="{{ Lang.site-just-using(VERSION) }}" class="fa-solid fa-flask"></i>
									<span>{{ Lang.site-just-using(VERSION) }}</span>
								</a>
							</li>
						</ul>
					</div>
				</nav>
			</aside>
			<div id="alert-msg">
				{{ show.displayMsg }}
			</div>
			<div id="body">
				<div id="content_mask">
					<div id="content" class="{{ runPlugin.getName }}-admin">
						{% IF runPlugin.getParamTemplate %}
							<a title="{{ Lang.core-settings }}" data-fancybox id="param_link" href="#" data-src="#param_panel">
								<i class="fa-solid fa-screwdriver-wrench"></i>
							</a>
							<div id="param_panel">
								<section class="content">
									<header>
										<h2>{{ Lang.core-settings }}</h2>
									</header>
									{% INCLUDE runPlugin.getParamTemplate %}
								</section>
							</div>
						{% ENDIF %}
						{% IF runPlugin.getHelpTemplate %}
							<a title="{{ Lang.core-help }}" data-fancybox id="help_link" href="#" data-src="#help_panel">
								<i class="fa-solid fa-circle-question"></i>
							</a>
							<div id="help_panel">
								<section class="content">
									<header>
										<h2>{{ Lang.core-help }}</h2>
									</header>
									{% INCLUDE runPlugin.getHelpTemplate %}
								</section>
							</div>
						{% ENDIF %}
						{% HOOK.adminToolsTemplates %}
						<div id="page-infos">
							<h2>{{ runPlugin.getTranslatedName() }}</h2>
							{% HOOK.afterAdminTitle %}
						</div>
						{{ CONTENT }}
					</div>
				</div>
			</div>
		</div>
		{% HOOK.endAdminBody %}
	</body>
</html>
