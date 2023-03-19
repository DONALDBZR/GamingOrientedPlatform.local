<?php
ini_set('max_execution_time', '300');
set_time_limit(300);
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/Router.php";
error_reporting(E_ERROR | E_PARSE);
echo "{$_SERVER['REQUEST_METHOD']} {$_SERVER['REQUEST_URI']} {$_SERVER['SERVER_PROTOCOL']}";
var_dump($_SESSION["parameter"]);
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        switch ($_SERVER['REQUEST_URI']) {
            case '/':
                if (isset($_SESSION['User'])) {
                    if (isset($_SESSION['User']['otp'])) {
                        header("Location: /Login/Verification/{$_SESSION['User']['username']}");
                    } else {
                        header("Location: /Users/Home/{$_SESSION['User']['username']}");
                    }
                } else {
                    $Router = new Router("GET", "/", "/Views/Homepage.php");
                }
                break;
            case '/Register':
                if (isset($_SESSION['User'])) {
                    if (isset($_SESSION['User']['otp'])) {
                        header("Location: /Login/Verification/{$_SESSION['User']['username']}");
                    } else {
                        header("Location: /Users/Home/{$_SESSION['User']['username']}");
                    }
                } else {
                    $Router = new Router("GET", "/Register", "/Views/Register.php");
                }
                break;
            case '/Login':
                if (isset($_SESSION['User'])) {
                    if (isset($_SESSION['User']['otp'])) {
                        header("Location: /Login/Verification/{$_SESSION['User']['username']}");
                    } else {
                        header("Location: /Users/Home/{$_SESSION['User']['username']}");
                    }
                } else {
                    $Router = new Router("GET", "/Login", "/Views/Login.php");
                }
                break;
                //     case "/Login/Verification/{$_SESSION['User']['username']}":
                //         if (isset($_SESSION['User'])) {
                //             if (!isset($_SESSION['User']['otp'])) {
                //                 header("Location: /Users/Home/{$_SESSION['User']['username']}");
                //             } else {
                //                 $Router = new Router("GET", "/Login/Verification/{$_SESSION['User']['username']}", "/Views/LoginVerification.php");
                //             }
                //         } else {
                //             header("Location: /");
                //         }
                //         break;
                //     case "/Users/Home/{$_SESSION['User']['username']}":
                //         if (isset($_SESSION['User'])) {
                //             if (isset($_SESSION['User']['otp'])) {
                //                 header("Location: /Login/Verification/{$_SESSION['User']['username']}");
                //             } else {
                //                 $Router = new Router("GET", "/Users/Home/{$_SESSION['User']['username']}", "/Views/UsersHome.php");
                //             }
                //         } else {
                //             header("Location: /");
                //         }
                //         break;
                //     case '/Users/CurrentUser':
                //         if (isset($_SESSION['User'])) {
                //             if (isset($_SESSION['User']['otp'])) {
                //                 header("Location: /Login/Verification/{$_SESSION['User']['username']}");
                //             } else {
                //                 $Router = new Router("GET", "/Users/CurrentUser", "/Controllers/CurrentUser.php");
                //             }
                //         } else {
                //             header("Location: /Sign-Out");
                //         }
                //         break;
                //     case '/Sign-Out':
                //         if (isset($_SESSION)) {
                //             $Router = new Router("GET", "/Sign-Out", "/Views/SignOut.php");
                //         } else {
                //             header("Location: /");
                //         }
                //         break;
                //     case '/LogOut':
                //         if (isset($_SESSION)) {
                //             $Router = new Router("GET", "/LogOut", "/Controllers/SignOut.php");
                //         } else {
                //             header("Location: /");
                //         }
                //         break;
                //     case '/ForgotPassword':
                //         if (isset($_SESSION['User'])) {
                //             if (isset($_SESSION['User']['otp'])) {
                //                 header("Location: /Login/Verification/{$_SESSION['User']['username']}");
                //             } else {
                //                 header("Location: /Users/Home/{$_SESSION['User']['username']}");
                //             }
                //         } else {
                //             $Router = new Router("GET", "/ForgotPassword", "/Views/ForgotPassword.php");
                //         }
                //         break;
                //     case "/Users/Profile/{$_SESSION['User']['username']}":
                //         if (isset($_SESSION['User'])) {
                //             if (isset($_SESSION['User']['otp'])) {
                //                 header("Location: /Login/Verification/{$_SESSION['User']['username']}");
                //             } else {
                //                 $Router = new Router("GET", "/Users/Profile/{$_SESSION['User']['username']}", "/Views/UsersProfile.php");
                //             }
                //         } else {
                //             header("Location: /");
                //         }
                //         break;
                //     case "/Users/Edit/Profile/{$_SESSION['User']['username']}":
                //         if (isset($_SESSION['User'])) {
                //             if (isset($_SESSION['User']['otp'])) {
                //                 header("Location: /Login/Verification/{$_SESSION['User']['username']}");
                //             } else {
                //                 $Router = new Router("GET", "/Users/Edit/Profile/{$_SESSION['User']['username']}", "/Views/UsersEditProfile.php");
                //             }
                //         } else {
                //             header("Location: /");
                //         }
                //         break;
                //     case "/Users/Security/{$_SESSION['User']['username']}":
                //         if (isset($_SESSION['User'])) {
                //             if (isset($_SESSION['User']['otp'])) {
                //                 header("Location: /Login/Verification/{$_SESSION['User']['username']}");
                //             } else {
                //                 $Router = new Router("GET", "/Users/Security/{$_SESSION['User']['username']}", "/Views/UsersSecurity.php");
                //             }
                //         } else {
                //             header("Location: /");
                //         }
                //         break;
                //     case "/Users/Accounts/{$_SESSION['User']['username']}":
                //         if (isset($_SESSION['User'])) {
                //             if (isset($_SESSION['User']['otp'])) {
                //                 header("Location: /Login/Verification/{$_SESSION['User']['username']}");
                //             } else {
                //                 $Router = new Router("GET", "/Users/Accounts/{$_SESSION['User']['username']}", "/Views/UsersAccounts.php");
                //             }
                //         } else {
                //             header("Location: /");
                //         }
                //         break;
                //     case "/LeagueOfLegends/Home/" . rawurlencode($_SESSION['Account']['LeagueOfLegends']['gameName']):
                //         if (isset($_SESSION['User'])) {
                //             if (isset($_SESSION['User']['otp'])) {
                //                 header("Location: /Login/Verification/{$_SESSION['User']['username']}");
                //             } else {
                //                 if (isset($_SESSION['Account']['LeagueOfLegends'])) {
                //                     $Router = new Router("GET", "/LeagueOfLegends/Home/" . rawurlencode($_SESSION['Account']['LeagueOfLegends']['gameName']), "/Views/LeagueOfLegendsHome.php");
                //                 } else {
                //                     header("Location: /Users/Home/{$_SESSION['User']['username']}");
                //                 }
                //             }
                //         } else {
                //             header("Location: /");
                //         }
                //         break;
                //     case '/LegendsOfLegends/CurrentSummoner':
                //         if (isset($_SESSION['User'])) {
                //             if (isset($_SESSION['User']['otp'])) {
                //                 header("Location: /Login/Verification/{$_SESSION['User']['username']}");
                //             } else {
                //                 if (isset($_SESSION['Account']['LeagueOfLegends'])) {
                //                     $Router = new Router("GET", "/LegendsOfLegends/CurrentSummoner", "/Controllers/CurrentSummoner.php");
                //                 } else {
                //                     header("Location: /Users/Home/{$_SESSION['User']['username']}");
                //                 }
                //             }
                //         } else {
                //             header("Location: /");
                //         }
                //         break;
                //     case '/LegendsOfLegends/MatchHistories':
                //         if (isset($_SESSION['User'])) {
                //             if (isset($_SESSION['User']['otp'])) {
                //                 header("Location: /Login/Verification/{$_SESSION['User']['username']}");
                //             } else {
                //                 if (isset($_SESSION['Account']['LeagueOfLegends'])) {
                //                     $Router = new Router("GET", "/LegendsOfLegends/MatchHistories", "/Controllers/MatchHistories.php");
                //                 } else {
                //                     header("Location: /Users/Home/{$_SESSION['User']['username']}");
                //                 }
                //             }
                //         } else {
                //             header("Location: /");
                //         }
                //         break;
                //     case "/LeagueOfLegends/Profile/" . rawurlencode($_SESSION['Search']['LeagueOfLegends']["gameName"]):
                //         if (isset($_SESSION['User'])) {
                //             if (isset($_SESSION['User']['otp'])) {
                //                 header("Location: /Login/Verification/{$_SESSION['User']['username']}");
                //             } else {
                //                 if (isset($_SESSION['Search']['LeagueOfLegends'])) {
                //                     $Router = new Router("GET", "/LeagueOfLegends/Profile/" . rawurlencode($_SESSION['Search']['LeagueOfLegends']["gameName"]), "/Views/LeagueOfLegendsProfile.php");
                //                 } else {
                //                     header("Location: /Users/Home/{$_SESSION['User']['username']}");
                //                 }
                //             }
                //         } else {
                //             header("Location: /");
                //         }
                //         break;
                //     case '/LegendsOfLegends/Search/Summoner':
                //         if (isset($_SESSION['User'])) {
                //             if (isset($_SESSION['User']['otp'])) {
                //                 header("Location: /Login/Verification/{$_SESSION['User']['username']}");
                //             } else {
                //                 if (isset($_SESSION['Account']['LeagueOfLegends'])) {
                //                     $Router = new Router("GET", "/LegendsOfLegends/Search/Summoner", "/Controllers/SearchSummoner.php");
                //                 } else {
                //                     header("Location: /Users/Home/{$_SESSION['User']['username']}");
                //                 }
                //             }
                //         } else {
                //             header("Location: /");
                //         }
                //         break;
                //     case '/LegendsOfLegends/ChampionMastery':
                //         if (isset($_SESSION['User'])) {
                //             if (isset($_SESSION['User']['otp'])) {
                //                 header("Location: /Login/Verification/{$_SESSION['User']['username']}");
                //             } else {
                //                 if (isset($_SESSION['Account']['LeagueOfLegends'])) {
                //                     $Router = new Router("GET", "/LegendsOfLegends/ChampionMastery", "/Controllers/ChampionMastery.php");
                //                 } else {
                //                     header("Location: /Users/Home/{$_SESSION['User']['username']}");
                //                 }
                //             }
                //         } else {
                //             header("Location: /");
                //         }
                //         break;
                //     case '/LegendsOfLegends/PlatformStatus':
                //         if (isset($_SESSION['User'])) {
                //             if (isset($_SESSION['User']['otp'])) {
                //                 header("Location: /Login/Verification/{$_SESSION['User']['username']}");
                //             } else {
                //                 if (isset($_SESSION['Account']['LeagueOfLegends'])) {
                //                     $Router = new Router("GET", "/LegendsOfLegends/PlatformStatus", "/Controllers/PlatformStatus.php");
                //                 } else {
                //                     header("Location: /Users/Home/{$_SESSION['User']['username']}");
                //                 }
                //             }
                //         } else {
                //             header("Location: /");
                //         }
                //         break;
                //     case '/LegendsOfLegends/PatchNotes':
                //         if (isset($_SESSION['User'])) {
                //             if (isset($_SESSION['User']['otp'])) {
                //                 header("Location: /Login/Verification/{$_SESSION['User']['username']}");
                //             } else {
                //                 if (isset($_SESSION['Account']['LeagueOfLegends'])) {
                //                     $Router = new Router("GET", "/LegendsOfLegends/PatchNotes", "/Controllers/PatchNotes.php");
                //                 } else {
                //                     header("Location: /Users/Home/{$_SESSION['User']['username']}");
                //                 }
                //             }
                //         } else {
                //             header("Location: /");
                //         }
                //         break;
                //     case '/PlayerUnknownBattleGrounds/CurrentPlayer':
                //         if (isset($_SESSION['User'])) {
                //             if (isset($_SESSION['User']['otp'])) {
                //                 header("Location: /Login/Verification/{$_SESSION['User']['username']}");
                //             } else {
                //                 if (isset($_SESSION['Account']['PlayerUnknownBattleGrounds'])) {
                //                     $Router = new Router("GET", "/PlayerUnknownBattleGrounds/CurrentPlayer", "/Controllers/CurrentPlayer.php");
                //                 } else {
                //                     header("Location: /Users/Home/{$_SESSION['User']['username']}");
                //                 }
                //             }
                //         } else {
                //             header("Location: /");
                //         }
                //         break;
            case '/Users/New':
                $Router = new Router("POST", "/Users/New", "/Controllers/Register.php");
                break;
            case "/Users":
                $Router = new Router("POST", "/Users", "/Controllers/Login.php");
                unset($_SESSION['parameter']);
                break;
        }
        break;
    case 'POST':
        switch ($_SERVER['REQUEST_URI']) {
            case '/Register':
            case '/Users/New':
                $Router = new Router("POST", "/Register", "/Controllers/Register.php");
                break;
            case '/Login':
            case "/Users":
                $Router = new Router("POST", "/Login", "/Controllers/Login.php");
                unset($_COOKIE);
                break;
                // case "/Login/Verification/{$_SESSION['User']['username']}":
                //     $Router = new Router("POST", "/Login/Verification/{$_SESSION['User']['username']}", "/Controllers/LoginVerification.php");
                //     break;
                // case '/ForgotPassword':
                //     $Router = new Router("POST", "/ForgotPassword", "/Controllers/ForgotPassword.php");
                //     break;
                // case "/Users/Edit/Profile/{$_SESSION['User']['username']}":
                //     $Router = new Router("POST", "/Users/Edit/Profile/{$_SESSION['User']['username']}", "/Controllers/UsersEditProfile.php");
                //     break;
                // case "/Users/Security/{$_SESSION['User']['username']}":
                //     $Router = new Router("POST", "/Users/Security/{$_SESSION['User']['username']}", "/Controllers/UsersSecurity.php");
                //     break;
                // case "/Users/Accounts/{$_SESSION['User']['username']}":
                //     $Router = new Router("POST", "/Users/Accounts/{$_SESSION['User']['username']}", "/Controllers/UsersAccounts.php");
                //     break;
                // case "/LeagueOfLegends/Home/{$_SESSION['Account']['LeagueOfLegends']['gameName']}":
                //     $Router = new Router("POST", "/LeagueOfLegends/Home/{$_SESSION['Account']['LeagueOfLegends']['gameName']}", "/Controllers/LeagueOfLegendsHome.php");
                //     break;
                // case "/LeagueOfLegends/Delete":
                //     $Router = new Router("POST", "/LeagueOfLegends/Delete", "/Controllers/LeagueOfLegendsDelete.php");
                //     break;
        }
}
