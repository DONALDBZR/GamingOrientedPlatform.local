/**
 * The Application that is going to be rendered in the DOM
 */
class Application extends React.Component {
    constructor(props) {
        super(props);
        /**
         * States of the properties of the component
         */
        this.state = {
            User: {
                username: "",
                mailAddress: "",
                profilePicture: "",
            },
            Accounts: {
                LeagueOfLegends: {
                    playerUniversallyUniqueIdentifier: "",
                    gameName: "",
                    tagLine: "",
                    Summoner: {
                        level: 0,
                        summonerIcon: 0,
                        soloDuoTier: "",
                        soloDuoDivision: "",
                        soloDuoLeaguePoints: 0,
                        soloDuoWinRate: 0.0,
                        flexTier: "",
                        flexDivision: "",
                        flexLeaguePoints: 0,
                        flexWinRate: 0.0,
                        kdaRatio: 0.0,
                        csMin: 0.0,
                        vsMin: 0.0,
                    },
                },
                PlayerUnknownBattleGrounds: {
                    identifier: "",
                    playerName: "",
                    platform: "",
                    Player: {
                        account: 200,
                        lifetime: 200,
                        Duo: {
                            winrate: 0.0,
                            top10Probability: 0.0,
                        },
                        Solo: {
                            winrate: 0.0,
                            top10Probability: 0.0,
                        },
                        Squad: {
                            winrate: 0.0,
                            top10Probability: 0.0,
                        },
                        kda: 0.0,
                        killStreak: 0,
                        longestKill: 0.0,
                        headshot: 0.0,
                        damagePerMatch: 0.0,
                    },
                },
            },
        };
    }
    /**
     * Retrieving every data needed for processing
     */
    retrieveData() {
        this.getCurrentUser();
        this.getSummonerData();
        this.getPlayerData();
    }
    /**
     * Verifying the state before rendering the link
     */
    verifyUser_username() {
        if (this.state.User.profilePicture != null) {
            return (
                <a href={`/Users/Profile/${this.state.User.username}`}>
                    <img src={this.state.User.profilePicture} />
                </a>
            );
        } else {
            return (
                <a
                    href={`/Users/Profile/${this.state.User.username}`}
                    class="fa fa-user"
                ></a>
            );
        }
    }
    /**
     * Verifying the state before rendering the link
     */
    verifyAccount_Riot_ID() {
        if (
            this.state.Accounts.LeagueOfLegends
                .playerUniversallyUniqueIdentifier != null
        ) {
            return (
                <div id="leagueOfLegendsCard">
                    <div>
                        <a
                            href={`/LeagueOfLegends/Home/${this.state.Accounts.LeagueOfLegends.gameName}`}
                        >
                            <img src="/Public/Images/League Of Legends Logo.png" />
                        </a>
                    </div>
                    <div>
                        <div>
                            <img
                                src={`http://ddragon.leagueoflegends.com/cdn/12.22.1/img/profileicon/${this.state.Accounts.LeagueOfLegends.Summoner.summonerIcon}.png`}
                            />
                            <div>
                                Level{" "}
                                {
                                    this.state.Accounts.LeagueOfLegends.Summoner
                                        .level
                                }
                            </div>
                            <div>
                                {this.state.Accounts.LeagueOfLegends.gameName}
                            </div>
                        </div>
                        <div>
                            <div>
                                <div>Solo/Duo</div>
                                <div>
                                    <img
                                        src={this.verifyLeagueOfLegends_rank_emblem(
                                            this.state.Accounts.LeagueOfLegends
                                                .Summoner.soloDuoTier
                                        )}
                                    />
                                </div>
                                <div>
                                    <div>
                                        {this.verifyLeagueOfLegends_rank(
                                            this.state.Accounts.LeagueOfLegends
                                                .Summoner.soloDuoTier,
                                            this.state.Accounts.LeagueOfLegends
                                                .Summoner.soloDuoDivision,
                                            this.state.Accounts.LeagueOfLegends
                                                .Summoner.soloDuoLeaguePoints
                                        )}
                                    </div>
                                    <div>Win Rate:</div>
                                    <div
                                        style={{
                                            color: this.verifyLeagueOfLegends_winRate(
                                                this.state.Accounts
                                                    .LeagueOfLegends.Summoner
                                                    .soloDuoWinRate
                                            ),
                                        }}
                                    >{`${this.state.Accounts.LeagueOfLegends.Summoner.soloDuoWinRate} %`}</div>
                                </div>
                            </div>
                            <div>
                                <div>Flex 5v5</div>
                                <div>
                                    <img
                                        src={this.verifyLeagueOfLegends_rank_emblem(
                                            this.state.Accounts.LeagueOfLegends
                                                .Summoner.flexTier
                                        )}
                                    />
                                </div>
                                <div>
                                    <div>
                                        {this.verifyLeagueOfLegends_rank(
                                            this.state.Accounts.LeagueOfLegends
                                                .Summoner.flexTier,
                                            this.state.Accounts.LeagueOfLegends
                                                .Summoner.flexDivision,
                                            this.state.Accounts.LeagueOfLegends
                                                .Summoner.flexLeaguePoints
                                        )}
                                    </div>
                                    <div>Win Rate:</div>
                                    <div
                                        style={{
                                            color: this.verifyLeagueOfLegends_winRate(
                                                this.state.Accounts
                                                    .LeagueOfLegends.Summoner
                                                    .flexWinRate
                                            ),
                                        }}
                                    >{`${this.state.Accounts.LeagueOfLegends.Summoner.flexWinRate} %`}</div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div>
                                <div>KDA:</div>
                                <div
                                    style={{
                                        color: this.verifyLeagueOfLegends_kda(
                                            this.state.Accounts.LeagueOfLegends
                                                .Summoner.kdaRatio
                                        ),
                                    }}
                                >
                                    {
                                        this.state.Accounts.LeagueOfLegends
                                            .Summoner.kdaRatio
                                    }
                                </div>
                            </div>
                            <div>
                                <div>CS/Min:</div>
                                <div
                                    style={{
                                        color: this.verifyLeagueOfLegends_csMin(
                                            this.state.Accounts.LeagueOfLegends
                                                .Summoner.csMin
                                        ),
                                    }}
                                >
                                    {
                                        this.state.Accounts.LeagueOfLegends
                                            .Summoner.csMin
                                    }
                                </div>
                            </div>
                            <div>
                                <div>VS/Min:</div>
                                <div
                                    style={{
                                        color: this.verifyLeagueOfLegends_vsMin(
                                            this.state.Accounts.LeagueOfLegends
                                                .Summoner.vsMin
                                        ),
                                    }}
                                >
                                    {
                                        this.state.Accounts.LeagueOfLegends
                                            .Summoner.vsMin
                                    }
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            );
        } else {
            return (
                <div>
                    You should add your account for League of Legends before
                    having accessed to the required content. You can click{" "}
                    <a href={`/Users/Accounts/${this.state.User.username}`}>
                        here
                    </a>{" "}
                    to process into adding your account!
                </div>
            );
        }
    }
    /**
     * Retrieving data from Riot Games data center for the user
     */
    getSummonerData() {
        fetch("/LegendsOfLegends/CurrentSummoner", {
            method: "GET",
        })
            .then((response) => response.json())
            .then((data) =>
                this.setState({
                    Accounts: {
                        LeagueOfLegends: {
                            Summoner: {
                                level: data.summonerLevel,
                                summonerIcon: data.profileIconId,
                                soloDuoTier: data.soloDuoTier,
                                soloDuoDivision: data.soloDuoRank,
                                soloDuoLeaguePoints: data.soloDuoLeaguePoints,
                                soloDuoWinRate: data.soloDuoWinRate,
                                flexTier: data.flexTier,
                                flexDivision: data.flexRank,
                                flexLeaguePoints: data.flexLeaguePoints,
                                flexWinRate: data.flexWinRate,
                                kdaRatio: data.kdaRatio,
                                csMin: data.csMin,
                                vsMin: data.vsMin,
                            },
                        },
                    },
                })
            );
    }
    /**
     * Verifying the ranks of the player
     */
    verifyLeagueOfLegends_rank(tier, rank, point) {
        if (tier == "" || tier == null) {
            return "Unranked";
        } else {
            return `${tier} ${rank} - ${point} LP`;
        }
    }
    /**
     * Verifying the ranks of the player for the logo
     */
    verifyLeagueOfLegends_rank_emblem(tier) {
        if (tier == "" || tier == null) {
            return "/Public/Images/Ranks/Emblem_Unranked.png";
        } else {
            return `/Public/Images/Ranks/Emblem_${tier}.png`;
        }
    }
    /**
     * Verifying the winrate before styling it
     */
    verifyLeagueOfLegends_winRate(win_rate) {
        if (win_rate >= 50) {
            return "rgb(0%, 100%, 0%)";
        } else if (win_rate >= 40 && win_rate < 50) {
            return "rgb(100%, 100%, 0%)";
        } else {
            return "rgb(100%, 0%, 0%)";
        }
    }
    /**
     * Verifying the KDA before styling it
     */
    verifyLeagueOfLegends_kda(kda) {
        if (kda >= 4) {
            return "rgb(0%, 100%, 0%)";
        } else if (kda >= 1 && kda < 4) {
            return "rgb(100%, 100%, 0%)";
        } else {
            return "rgb(100%, 0%, 0%)";
        }
    }
    /**
     * Verifying the CS/Min before styling it
     */
    verifyLeagueOfLegends_csMin(cs_min) {
        if (cs_min >= 6) {
            return "rgb(0%, 100%, 0%)";
        } else if (cs_min >= 1 && cs_min < 6) {
            return "rgb(100%, 100%, 0%)";
        } else {
            return "rgb(100%, 0%, 0%)";
        }
    }
    /**
     * Verifying the VS/Min before styling it
     */
    verifyLeagueOfLegends_vsMin(vs_min) {
        if (vs_min >= 2) {
            return "rgb(0%, 100%, 0%)";
        } else if (vs_min >= 1 && vs_min < 2) {
            return "rgb(100%, 100%, 0%)";
        } else {
            return "rgb(100%, 0%, 0%)";
        }
    }
    /**
     * Retrieving data from PUBG Corporations data center for the user
     */
    getPlayerData() {
        fetch("/PlayerUnknownBattleGrounds/CurrentPlayer", {
            method: "GET",
        })
            .then((response) => response.json())
            .then((data) => {
                this.setState({
                    Player: {
                        account: data.httpResponseCode_account,
                        lifetime: data.httpResponseCode_lifetime,
                        Duo: {
                            winrate: data.duo.winrate,
                            top10Probability: data.duo.top10Probability,
                        },
                        Solo: {
                            winrate: data.solo.winrate,
                            top10Probability: data.solo.top10Probability,
                        },
                        Squad: {
                            winrate: data.squad.winrate,
                            top10Probability: data.squad.top10Probability,
                        },
                        kda: data.kda,
                        killStreak: data.killStreak,
                        longestKill: data.longestKill,
                        headshot: data.headshot,
                        damagePerMatch: data.damagePerMatch,
                    },
                });
            });
    }
    /**
     * Verifying the state before rendering the link
     */
    verifyAccount_PUBG_ID() {
        if (this.state.Accounts.PlayerUnknownBattleGrounds.identifier != null) {
            return (
                <div id="playerUnknownBattleGroundsCard">
                    <div>
                        <a
                            href={`/PlayerUnknownBattleGrounds/Home/${this.state.Accounts.PlayerUnknownBattleGrounds.playerName}`}
                        >
                            <img src="/Public/Images/PUBG RGB Logos (Web)/PUBG_BG_Full_Flat_White_2048.png" />
                        </a>
                    </div>
                    <div>
                        <i class="fa fa-steam"></i>
                    </div>
                    <div>
                        <div>
                            <div>Solo</div>
                            <div>
                                <div>Win Rate:</div>
                                <div
                                    style={{
                                        color: this.verifyPlayerUnknownBattleGrounds_winRate(
                                            this.state.Accounts
                                                .PlayerUnknownBattleGrounds
                                                .Player.Solo.winrate
                                        ),
                                    }}
                                >{`${this.state.Accounts.PlayerUnknownBattleGrounds.Player.Solo.winrate} %`}</div>
                            </div>
                            <div>
                                <div>Top 10's:</div>
                                <div
                                    style={{
                                        color: this.verifyPlayerUnknownBattleGrounds_top10Probability(
                                            this.state.Accounts
                                                .PlayerUnknownBattleGrounds
                                                .Player.Solo.top10Probability
                                        ),
                                    }}
                                >
                                    {`${this.state.Accounts.PlayerUnknownBattleGrounds.Player.Solo.top10Probability} %`}
                                </div>
                            </div>
                        </div>
                        <div>
                            <div>Duo</div>
                            <div>
                                <div>Win Rate:</div>
                                <div
                                    style={{
                                        color: this.verifyPlayerUnknownBattleGrounds_winRate(
                                            this.state.Accounts
                                                .PlayerUnknownBattleGrounds
                                                .Player.Duo.winrate
                                        ),
                                    }}
                                >{`${this.state.Accounts.PlayerUnknownBattleGrounds.Player.Duo.winrate} %`}</div>
                            </div>
                            <div>
                                <div>Top 10's:</div>
                                <div
                                    style={{
                                        color: this.verifyPlayerUnknownBattleGrounds_top10Probability(
                                            this.state.Accounts
                                                .PlayerUnknownBattleGrounds
                                                .Player.Duo.top10Probability
                                        ),
                                    }}
                                >
                                    {`${this.state.Accounts.PlayerUnknownBattleGrounds.Player.Duo.top10Probability} %`}
                                </div>
                            </div>
                        </div>
                        <div>
                            <div>Squad</div>
                            <div>
                                <div>Win Rate:</div>
                                <div
                                    style={{
                                        color: this.verifyPlayerUnknownBattleGrounds_winRate(
                                            this.state.pubgCard.squad.winrate
                                        ),
                                    }}
                                >{`${this.state.pubgCard.squad.winrate} %`}</div>
                            </div>
                            <div>
                                <div>Top 10's:</div>
                                <div
                                    style={{
                                        color: this.verifyPlayerUnknownBattleGrounds_top10Probability(
                                            this.state.Accounts
                                                .PlayerUnknownBattleGrounds
                                                .Player.Squad.top10Probability
                                        ),
                                    }}
                                >
                                    {`${this.state.Accounts.PlayerUnknownBattleGrounds.Player.Squad.top10Probability} %`}
                                </div>
                            </div>
                        </div>
                        <div>
                            <div>
                                <div>KDA:</div>
                                <div
                                    style={{
                                        color: this.verifyPlayerUnknownBattleGrounds_kda(
                                            this.state.Accounts
                                                .PlayerUnknownBattleGrounds
                                                .Player.kda
                                        ),
                                    }}
                                >
                                    {
                                        this.state.Accounts
                                            .PlayerUnknownBattleGrounds.Player
                                            .kda
                                    }
                                </div>
                            </div>
                            <div>
                                <div>Kill Streak:</div>
                                <div
                                    style={{
                                        color: this.verifyPlayerUnknownBattleGrounds_killStreak(
                                            this.state.Accounts
                                                .PlayerUnknownBattleGrounds
                                                .Player.killStreak
                                        ),
                                    }}
                                >
                                    {
                                        this.state.Accounts
                                            .PlayerUnknownBattleGrounds.Player
                                            .killStreak
                                    }
                                </div>
                            </div>
                            <div>
                                <div>Longest Kill Distance:</div>
                                <div
                                    style={{
                                        color: this.verifyPlayerUnknownBattleGrounds_longestKill(
                                            this.state.Accounts
                                                .PlayerUnknownBattleGrounds
                                                .Player.longestKill
                                        ),
                                    }}
                                >{`${this.state.Accounts.PlayerUnknownBattleGrounds.Player.longestKill} m`}</div>
                            </div>
                            <div>
                                <div>Headshot:</div>
                                <div
                                    style={{
                                        color: this.verifyPlayerUnknownBattleGrounds_headshot(
                                            this.state.Accounts
                                                .PlayerUnknownBattleGrounds
                                                .Player.headshot
                                        ),
                                    }}
                                >{`${this.state.Accounts.PlayerUnknownBattleGrounds.Player.headshot} %`}</div>
                            </div>
                            <div>
                                <div>Damage/Match:</div>
                                <div
                                    style={{
                                        color: this.verifyPlayerUnknownBattleGrounds_damagePerMatch(
                                            this.state.Accounts
                                                .PlayerUnknownBattleGrounds
                                                .Player.damagePerMatch
                                        ),
                                    }}
                                >
                                    {
                                        this.state.Accounts
                                            .PlayerUnknownBattleGrounds.Player
                                            .damagePerMatch
                                    }
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            );
        } else {
            return (
                <div>
                    You should add your account for PUBG before having accessed
                    to the required content. You can click{" "}
                    <a href={`/Users/Accounts/${this.state.User.username}`}>
                        here
                    </a>{" "}
                    to process into adding your account!
                </div>
            );
        }
    }
    /**
     * Verifying the winrate before styling it
     */
    verifyPlayerUnknownBattleGrounds_winRate(win_rate) {
        if (win_rate >= 50) {
            return "rgb(0%, 100%, 0%)";
        } else if (win_rate >= 1 && win_rate < 50) {
            return "rgb(100%, 100%, 0%)";
        } else {
            return "rgb(100%, 0%, 0%)";
        }
    }
    /**
     * Verifying the top 10 probability before styling it
     */
    verifyPlayerUnknownBattleGrounds_top10Probability(top10Probability) {
        if (top10Probability >= 50) {
            return "rgb(0%, 100%, 0%)";
        } else if (top10Probability >= 10 && top10Probability < 50) {
            return "rgb(100%, 100%, 0%)";
        } else {
            return "rgb(100%, 0%, 0%)";
        }
    }
    /**
     * Verifying the kda ratio before styling it
     */
    verifyPlayerUnknownBattleGrounds_kda(kda) {
        if (kda >= 6) {
            return "rgb(0%, 100%, 0%)";
        } else if (kda >= 1 && kda < 6) {
            return "rgb(100%, 100%, 0%)";
        } else {
            return "rgb(100%, 0%, 0%)";
        }
    }
    /**
     * Verifying the kill streak before styling it
     */
    verifyPlayerUnknownBattleGrounds_killStreak(killStreak) {
        if (killStreak >= 5) {
            return "rgb(0%, 100%, 0%)";
        } else if (killStreak >= 2 && killStreak < 5) {
            return "rgb(100%, 100%, 0%)";
        } else {
            return "rgb(100%, 0%, 0%)";
        }
    }
    /**
     * Verifying the longest kill distance before styling it
     */
    verifyPlayerUnknownBattleGrounds_longestKill(longestKill) {
        if (longestKill >= 200) {
            return "rgb(0%, 100%, 0%)";
        } else if (longestKill >= 100 && longestKill < 200) {
            return "rgb(100%, 100%, 0%)";
        } else {
            return "rgb(100%, 0%, 0%)";
        }
    }
    /**
     * Verifying the Headshot probability before styling it
     */
    verifyPlayerUnknownBattleGrounds_headshot(headshot) {
        if (headshot >= 50) {
            return "rgb(0%, 100%, 0%)";
        } else if (headshot >= 40 && headshot < 50) {
            return "rgb(100%, 100%, 0%)";
        } else {
            return "rgb(100%, 0%, 0%)";
        }
    }
    /**
     * Verifying the damage per match before styling it
     */
    verifyPlayerUnknownBattleGrounds_damagePerMatch(damagePerMatch) {
        if (damagePerMatch >= 250) {
            return "rgb(0%, 100%, 0%)";
        } else if (damagePerMatch >= 100 && damagePerMatch < 250) {
            return "rgb(100%, 100%, 0%)";
        } else {
            return "rgb(100%, 0%, 0%)";
        }
    }
    /**
     * Retrieving the user data
     */
    getCurrentUser() {
        fetch("/Users/CurrentUser", {
            method: "GET",
        })
            .then((response) => response.json())
            .then((data) =>
                this.setState({
                    User: {
                        username: data.User.username,
                        mailAddress: data.User.mailAddress,
                        profilePicture: data.User.profilePicture,
                    },
                    Accounts: {
                        LeagueOfLegends: {
                            gameName: data.Account.LeagueOfLegends.gameName,
                            tagLine: data.Account.LeagueOfLegends.tagLine,
                            playerUniversallyUniqueIdentifier:
                                data.Account.LeagueOfLegends
                                    .playerUniversallyUniqueIdentifier,
                        },
                        PlayerUnknownBattleGrounds: {
                            identifier:
                                data.Account.PlayerUnknownBattleGrounds
                                    .identifier,
                            platform:
                                data.Account.PlayerUnknownBattleGrounds
                                    .platform,
                            playerName:
                                data.Account.PlayerUnknownBattleGrounds
                                    .playerName,
                        },
                    },
                })
            );
    }
    /**
     * Renders the components that are being returned
     * @returns {Application} Component
     */
    render() {
        return [<Header />, <Main />, <Footer />];
    }
}
/**
 * The component that is the header
 */
class Header extends Application {
    constructor(props) {
        super(props);
    }
    /**
     * Methods to be run as soon as the component is mounted
     */
    componentDidMount() {
        this.getCurrentUser();
    }
    render() {
        return (
            <header>
                <nav>
                    <div>
                        <a href={`/Users/Home/${this.state.User.username}`}>
                            Parkinston
                        </a>
                    </div>
                    <div>{this.verifyUser_username()}</div>
                    <div>
                        <a href="/Sign-Out" class="fa fa-sign-out"></a>
                    </div>
                </nav>
            </header>
        );
    }
}
/**
 * The component that is the main
 */
class Main extends Application {
    constructor(props) {
        super(props);
    }
    /**
     * Methods to be run as soon as the component is mounted
     */
    componentDidMount() {
        this.retrieveData();
    }
    render() {
        return (
            <main>
                {this.verifyAccount_Riot_ID()}
                {this.verifyAccount_PUBG_ID()}
            </main>
        );
    }
}
/**
 * The component that is the footer
 */
class Footer extends Application {
    render() {
        return <footer>Parkinston</footer>;
    }
}
// Rendering the page
ReactDOM.render(<Application />, document.body);
