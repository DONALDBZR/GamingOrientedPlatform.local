<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/Models/Router.php";
$Router = new Router($_SERVER['REQUEST_URI']);
error_reporting(E_ERROR | E_PARSE);
switch ($Router->getRoute()) {
    case '/':
        if (isset($_SESSION['User'])) {
            if (isset($_SESSION['User']['otp'])) {
                header("Location: /Login/Verification/{$_SESSION['User']['username']}");
            } else {
                header("Location: /Users/Home/{$_SESSION['User']['username']}");
            }
        } else {
            $Router->get($Router->getRoute(), "/Views/Homepage.php");
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
            $Router->get($Router->getRoute(), "/Views/Register.php");
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
            $Router->get($Router->getRoute(), "/Views/Login.php");
        }
        break;
    case '/ForgotPassword':
        if (isset($_SESSION['User'])) {
            if (isset($_SESSION['User']['otp'])) {
                header("Location: /Login/Verification/{$_SESSION['User']['username']}");
            } else {
                header("Location: /Users/Home/{$_SESSION['User']['username']}");
            }
        } else {
            $Router->get($Router->getRoute(), "/Views/ForgotPassword.php");
        }
        break;
    case "/Login/Verification/{$_SESSION['User']['username']}":
        if (isset($_SESSION['User'])) {
            if (!isset($_SESSION['User']['otp'])) {
                header("Location: /Users/Home/{$_SESSION['User']['username']}");
            } else {
                $Router->get($Router->getRoute(), "/Views/LoginVerification.php");
            }
        } else {
            header("Location: /");
        }
        break;
    case '/Users/CurrentUser':
        $Router->get($Router->getRoute(), "/Controllers/CurrentUser.php");
        break;
    case "/Users/Home/{$_SESSION['User']['username']}":
        if (isset($_SESSION['User'])) {
            if (isset($_SESSION['User']['otp'])) {
                header("Location: /Login/Verification/{$_SESSION['User']['username']}");
            } else {
                $Router->get($Router->getRoute(), "/Views/UsersHome.php");
            }
        } else {
            header("Location: /");
        }
        break;
    case '/LeagueOfLegends/CurrentSummoner':
        $Router->get($Router->getRoute(), "/Controllers/CurrentSummoner.php");
        break;
    case '/PlayerUnknownBattleGrounds/CurrentPlayer':
        $Router->get($Router->getRoute(), "/Controllers/CurrentPlayer.php");
        break;
    case "/Users/Accounts/{$_SESSION['User']['username']}":
        if (isset($_SESSION['User'])) {
            if (isset($_SESSION['User']['otp'])) {
                header("Location: /Login/Verification/{$_SESSION['User']['username']}");
            } else {
                $Router->get($Router->getRoute(), "/Views/UsersAccounts.php");
            }
        } else {
            header("Location: /");
        }
        break;
    case '/LeagueOfLegends/Regions':
        $Router->get($Router->getRoute(), "/Controllers/RiotBaseRegions.php");
        break;
    case '/Sign-Out':
        if (isset($_SESSION)) {
            $Router->get($Router->getRoute(), "/Views/SignOut.php");
        } else {
            header("Location: /");
        }
        break;
    case '/LogOut':
        if (isset($_SESSION)) {
            $Router->get($Router->getRoute(), "/Controllers/SignOut.php");
        } else {
            header("Location: /");
        }
        break;
    case '/LeagueOfLegends/PatchNotes':
    case '/PlayerUnknownBattleGrounds/PatchNotes':
        $Router->get($Router->getRoute(), "/Controllers/PatchNotes.php");
        break;
    case "/Users/Profile/{$_SESSION['User']['username']}":
        if (isset($_SESSION['User'])) {
            if (isset($_SESSION['User']['otp'])) {
                header("Location: /Login/Verification/{$_SESSION['User']['username']}");
            } else {
                $Router->get($Router->getRoute(), "/Views/UsersProfile.php");
            }
        } else {
            header("Location: /");
        }
        break;
    case "/Users/Edit/Profile/{$_SESSION['User']['username']}":
        if (isset($_SESSION['User'])) {
            if (isset($_SESSION['User']['otp'])) {
                header("Location: /Login/Verification/{$_SESSION['User']['username']}");
            } else {
                $Router->get($Router->getRoute(), "/Views/UsersEditProfile.php");
            }
        } else {
            header("Location: /");
        }
        break;
    case "/Users/Security/{$_SESSION['User']['username']}":
        if (isset($_SESSION['User'])) {
            if (isset($_SESSION['User']['otp'])) {
                header("Location: /Login/Verification/{$_SESSION['User']['username']}");
            } else {
                $Router->get($Router->getRoute(), "/Views/UsersSecurity.php");
            }
        } else {
            header("Location: /");
        }
        break;
    case "/LeagueOfLegends/Home/" . rawurlencode($_SESSION['Account']['LeagueOfLegends']['gameName']):
        if (isset($_SESSION['User'])) {
            if (isset($_SESSION['User']['otp'])) {
                header("Location: /Login/Verification/{$_SESSION['User']['username']}");
            } else {
                if (isset($_SESSION['Account']['LeagueOfLegends'])) {
                    $Router->get($Router->getRoute(), "/Views/LeagueOfLegendsHome.php");
                } else {
                    header("Location: /Users/Home/{$_SESSION['User']['username']}");
                }
            }
        } else {
            header("Location: /");
        }
        break;
    case '/LeagueOfLegends/PlatformStatus':
        $Router->get($Router->getRoute(), "/Controllers/PlatformStatus.php");
        break;
    case '/LeagueOfLegends/ChampionMastery':
        $Router->get($Router->getRoute(), "/Controllers/ChampionMastery.php");
        break;
    case '/LeagueOfLegends/MatchHistories':
    case '/PlayerUnknownBattleGrounds/MatchHistory':
        $Router->get($Router->getRoute(), "/Controllers/MatchHistories.php");
        break;
    case "/LeagueOfLegends/Profile/" . rawurlencode($_SESSION['Search']['LeagueOfLegends']["gameName"]):
        if (isset($_SESSION['User'])) {
            if (isset($_SESSION['User']['otp'])) {
                header("Location: /Login/Verification/{$_SESSION['User']['username']}");
            } else {
                if (isset($_SESSION['Search']['LeagueOfLegends'])) {
                    $Router->get($Router->getRoute(), "/Views/LeagueOfLegendsProfile.php");
                } else {
                    header("Location: /Users/Home/{$_SESSION['User']['username']}");
                }
            }
        } else {
            header("Location: /");
        }
        break;
    case '/LeagueOfLegends/Search/Summoner':
        $Router->post($Router->getRoute(), "/Controllers/SearchSummoner.php");
        break;
    case '/Users/New':
        $Router->post($Router->getRoute(), "/Controllers/Register.php");
        break;
    case "/Users":
        $Router->post($Router->getRoute(), "/Controllers/Login.php");
        break;
    case "/Users/" . json_decode(file_get_contents("php://input"))->mailAddress . "/Password":
        $Router->post($Router->getRoute(), "/Controllers/ForgotPassword.php");
        break;
    case "/Passwords/{$_SESSION['User']['username']}":
        $Router->post($Router->getRoute(), "/Controllers/LoginVerification.php");
        break;
    case "/Users/{$_SESSION['User']['username']}/Accounts":
        $Router->post($Router->getRoute(), "/Controllers/UsersAccounts.php");
        break;
    case "/Users/{$_SESSION['User']['username']}/ProfilePicture":
        $Router = new Router($_SERVER['REQUEST_URI'], "/Controllers/UsersEditProfile.php");
        break;
    case "/Users/{$_SESSION['User']['username']}/Security":
        $Router->post($Router->getRoute(), "/Controllers/UsersSecurity.php");
        break;
    case "/LeagueOfLegends/Summoners":
        $Router->post($Router->getRoute(), "/Controllers/LeagueOfLegendsHome.php");
        break;
    case "/LeagueOfLegends/Refresh":
        $Router->post($Router->getRoute(), "/Controllers/LeagueOfLegendsDelete.php");
        break;
    case "/PlayerUnknownBattleGrounds/Home/" . rawurlencode($_SESSION['Account']['PlayerUnknownBattleGrounds']['playerName']):
        if (isset($_SESSION['User'])) {
            if (isset($_SESSION['User']['otp'])) {
                header("Location: /Login/Verification/{$_SESSION['User']['username']}");
            } else {
                if (isset($_SESSION['Account']['PlayerUnknownBattleGrounds'])) {
                    $Router->post($Router->getRoute(), "/views/PlayerUnknownBattleGroundsHome.php");
                } else {
                    header("Location: /Users/Home/{$_SESSION['User']['username']}");
                }
            }
        } else {
            header("Location: /");
        }
        break;
    case '/PlayerUnknownBattleGrounds/CurrentSeason':
        $Router->post($Router->getRoute(), "/Controllers/CurrentSeason.php");
        break;
}
