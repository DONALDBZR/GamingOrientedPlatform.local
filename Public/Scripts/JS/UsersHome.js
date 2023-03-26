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
                profilePicture: "",
            },
            Accounts: {
                LeagueOfLegends: {
                    playerUniversallyUniqueIdentifier: "",
                    gameName: "",
                    Summoner: {
                        profileIconId: 0,
                        level: 0,
                        kda: 0.0,
                        csMin: 0.0,
                        vsMin: 0.0,
                        SoloDuo: {
                            tier: "",
                            division: "",
                            leaguePoints: 0,
                            winRate: 0.0,
                        },
                        Flex5v5: {
                            tier: "",
                            division: "",
                            leaguePoints: 0,
                            winRate: 0.0,
                        },
                    },
                    Version: {
                        major: 0,
                        minor: 0,
                        patchNotes: 0,
                    },
                },
                PlayerUnknownBattleGrounds: {
                    identifier: "",
                    playerName: "",
                    Player: {
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
     * Verifying the state before rendering the link
     * @returns {object}
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
     * Retrieving data from Riot Games data center for the user
     * @returns {void}
     */
    getSummonerData() {
        fetch("/LeagueOfLegends/CurrentSummoner", {
            method: "GET",
        })
            .then((response) => response.json())
            .then((data) =>
                this.setState({
                    Accounts: {
                        LeagueOfLegends: {
                            Summoner: {
                                level: data.summonerLevel,
                                profileIconId: data.profileIconId,
                                kda: data.kda,
                                csMin: data.csMin,
                                vsMin: data.vsMin,
                                SoloDuo: {
                                    tier: data.SoloDuo.tier,
                                    division: data.SoloDuo.division,
                                    leaguePoints: data.SoloDuo.leaguePoints,
                                    winRate: data.SoloDuo.winRate,
                                },
                                Flex5v5: {
                                    tier: data.Flex5v5.tier,
                                    division: data.Flex5v5.division,
                                    leaguePoints: data.Flex5v5.leaguePoints,
                                    winRate: data.Flex5v5.winRate,
                                },
                            },
                        },
                    },
                })
            );
    }
    /**
     * Verifying the ranks of the player
     * @param {object} mode
     * @returns {string}
     */
    verifyLeagueOfLegends_rank(mode) {
        if (mode.tier == "" || mode.tier == null) {
            return "Unranked";
        } else {
            return `${mode.tier} ${mode.division} - ${mode.leaguePoints} LP`;
        }
    }
    /**
     * Verifying the ranks of the player for the logo
     * @param {string} tier
     * @returns {string}
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
     * @param {number} win_rate
     * @returns {string}
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
     * @param {number} kda
     * @returns {string}
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
     * @param {number} cs_min
     * @returns {string}
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
     * @param {number} vs_min
     * @returns {string}
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
     * @returns {void}
     */
    getPlayerData() {
        fetch("/PlayerUnknownBattleGrounds/CurrentPlayer", {
            method: "GET",
        })
            .then((response) => response.json())
            .then((data) =>
                this.setState({
                    Accounts: {
                        PlayerUnknownBattleGrounds: {
                            Player: {
                                Duo: {
                                    winrate: data.duo.winrate,
                                    top10Probability: data.duo.top10Probability,
                                },
                                Solo: {
                                    winrate: data.solo.winrate,
                                    top10Probability:
                                        data.solo.top10Probability,
                                },
                                Squad: {
                                    winrate: data.squad.winrate,
                                    top10Probability:
                                        data.squad.top10Probability,
                                },
                                kda: data.kda,
                                killStreak: data.killStreak,
                                longestKill: data.longestKill,
                                headshot: data.headshot,
                                damagePerMatch: data.damagePerMatch,
                            },
                        },
                    },
                })
            );
    }
    /**
     * Verifying the winrate before styling it
     * @param {number} win_rate
     * @returns {string}
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
     * @param {number} top10Probability
     * @returns {string}
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
     * @param {number} kda
     * @returns {string}
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
     * @param {number} killStreak
     * @returns {string}
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
     * @param {number} longestKill
     * @returns {string}
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
     * @param {number} headshot
     * @returns {string}
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
     * @param {number} damagePerMatch
     * @returns {string}
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
     * @returns {void}
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
                        profilePicture: data.User.profilePicture,
                    },
                    Accounts: {
                        LeagueOfLegends: {
                            gameName: data.Account.LeagueOfLegends.gameName,
                            playerUniversallyUniqueIdentifier:
                                data.Account.LeagueOfLegends
                                    .playerUniversallyUniqueIdentifier,
                        },
                        PlayerUnknownBattleGrounds: {
                            identifier:
                                data.Account.PlayerUnknownBattleGrounds
                                    .identifier,
                            playerName:
                                data.Account.PlayerUnknownBattleGrounds
                                    .playerName,
                        },
                    },
                })
            );
    }
    /**
     * Retrieving the version of the product
     * @returns {void}
     */
    getVersion() {
        fetch("/LeagueOfLegends/PatchNotes", {
            method: "GET",
        })
            .then((response) => response.json())
            .then((data) =>
                this.setState({
                    Accounts: {
                        LeagueOfLegends: {
                            Version: {
                                major: data.major,
                                minor: data.minor,
                                patchNotes: data.patchNotes,
                            },
                        },
                    },
                })
            );
    }
    /**
     * Renders the components that are being returned
     * @returns {object}
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
        this.getCurrentUser();
    }
    render() {
        return (
            <main>
                <LeagueOfLegends
                    gameName={this.state.Accounts.LeagueOfLegends.gameName}
                    playerUniversallyUniqueIdentifier={
                        this.state.Accounts.LeagueOfLegends
                            .playerUniversallyUniqueIdentifier
                    }
                    username={this.state.User.username}
                />
                <PlayerUnknownBattleGrounds
                    playerName={
                        this.state.Accounts.PlayerUnknownBattleGrounds
                            .playerName
                    }
                    identifier={
                        this.state.Accounts.PlayerUnknownBattleGrounds
                            .identifier
                    }
                    username={this.state.User.username}
                />
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
/**
 * The component that will have all the properties and states for the card
 */
class LeagueOfLegends extends Main {
    constructor(props) {
        super(props);
        this.state = {
            Accounts: {
                LeagueOfLegends: {
                    gameName: "",
                    Summoner: {
                        profileIconId: 0,
                        level: 0,
                        SoloDuo: {
                            tier: "",
                            division: "",
                            leaguePoints: 0,
                            winRate: 0.0,
                        },
                        Flex5v5: {
                            tier: "",
                            division: "",
                            leaguePoints: 0,
                            winRate: 0.0,
                        },
                        kda: 0.0,
                        csMin: 0.0,
                        vsMin: 0.0,
                    },
                },
            },
        };
    }
    componentDidMount() {
        if (this.props.playerUniversallyUniqueIdentifier != null) {
            this.getSummonerData();
        }
    }
    render() {
        if (this.props.playerUniversallyUniqueIdentifier != null) {
            return (
                <div id="leagueOfLegendsCard">
                    <div>
                        <a
                            href={`/LeagueOfLegends/Home/${this.props.gameName}`}
                        >
                            <img src="/Public/Images/League Of Legends Logo.png" />
                        </a>
                    </div>
                    <div>
                        <div>
                            <DataDragon
                                profileIconIdentifier={
                                    this.state.Accounts.LeagueOfLegends.Summoner
                                        .profileIconId
                                }
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
                                                .Summoner.SoloDuo.tier
                                        )}
                                    />
                                </div>
                                <div>
                                    <div>
                                        {this.verifyLeagueOfLegends_rank(
                                            this.state.Accounts.LeagueOfLegends
                                                .Summoner.SoloDuo
                                        )}
                                    </div>
                                    <div>Win Rate:</div>
                                    <div
                                        style={{
                                            color: this.verifyLeagueOfLegends_winRate(
                                                this.state.Accounts
                                                    .LeagueOfLegends.Summoner
                                                    .SoloDuo.winRate
                                            ),
                                        }}
                                    >{`${this.state.Accounts.LeagueOfLegends.Summoner.SoloDuo.winRate} %`}</div>
                                </div>
                            </div>
                            <div>
                                <div>Flex 5v5</div>
                                <div>
                                    <img
                                        src={this.verifyLeagueOfLegends_rank_emblem(
                                            this.state.Accounts.LeagueOfLegends
                                                .Summoner.Flex5v5.tier
                                        )}
                                    />
                                </div>
                                <div>
                                    <div>
                                        {this.verifyLeagueOfLegends_rank(
                                            this.state.Accounts.LeagueOfLegends
                                                .Summoner.Flex5v5
                                        )}
                                    </div>
                                    <div>Win Rate:</div>
                                    <div
                                        style={{
                                            color: this.verifyLeagueOfLegends_winRate(
                                                this.state.Accounts
                                                    .LeagueOfLegends.Summoner
                                                    .Flex5v5.winRate
                                            ),
                                        }}
                                    >{`${this.state.Accounts.LeagueOfLegends.Summoner.Flex5v5.winRate} %`}</div>
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
                                                .Summoner.kda
                                        ),
                                    }}
                                >
                                    {
                                        this.state.Accounts.LeagueOfLegends
                                            .Summoner.kda
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
                    <a href={`/Users/Accounts/${this.props.username}`}>here</a>{" "}
                    to process into adding your account!
                </div>
            );
        }
    }
}
class PlayerUnknownBattleGrounds extends Main {
    constructor(props) {
        super(props);
        this.state = {
            Accounts: {
                PlayerUnknownBattleGrounds: {
                    Player: {
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
    componentDidMount() {
        if (this.props.identifier != null) {
            this.getPlayerData();
        }
    }
    render() {
        if (this.props.identifier != null) {
            return (
                <div id="playerUnknownBattleGroundsCard">
                    <div>
                        <a
                            href={`/PlayerUnknownBattleGrounds/Home/${this.props.playerName}`}
                        >
                            <img src="/Public/Images/PUBG RGB Logos (Web)/PUBG_BG_Full_Flat_White_2048.png" />
                        </a>
                    </div>
                    <div>
                        <div>{this.props.playerName}</div>
                        <div>
                            <i class="fa fa-steam"></i>
                        </div>
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
                                            this.state.Accounts
                                                .PlayerUnknownBattleGrounds
                                                .Player.Squad.winrate
                                        ),
                                    }}
                                >{`${this.state.Accounts.PlayerUnknownBattleGrounds.Player.Squad.winrate} %`}</div>
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
                    <a href={`/Users/Accounts/${this.props.username}`}>here</a>{" "}
                    to process into adding your account!
                </div>
            );
        }
    }
}
/**
 * Data Dragon Component
 */
class DataDragon extends LeagueOfLegends {
    constructor(props) {
        super(props);
        this.state = {
            Accounts: {
                LeagueOfLegends: {
                    Version: {
                        major: 0,
                        minor: 0,
                        patchNotes: 0,
                    },
                },
            },
        };
    }
    componentDidMount() {
        this.getVersion();
    }
    render() {
        return (
            <img
                src={`http://ddragon.leagueoflegends.com/cdn/${this.state.Accounts.LeagueOfLegends.Version.major}.${this.state.Accounts.LeagueOfLegends.Version.minor}.${this.state.Accounts.LeagueOfLegends.Version.patchNotes}/img/profileicon/${this.props.profileIconIdentifier}.png`}
            />
        );
    }
}
// Rendering the page
ReactDOM.render(<Application />, document.body);
