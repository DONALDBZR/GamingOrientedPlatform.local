/**
 * The Application that is going to be rendered in the DOM
 */
class Application extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            User: {
                username: "",
                profilePicture: "",
            },
            Accounts: {
                PlayerUnknownBattleGrounds: {
                    playerName: "",
                    search: "",
                    Version: {
                        major: 0,
                        minor: 0,
                    },
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
                    Season: {
                        Solo: {
                            tier: "",
                            division: 0,
                            point: 0,
                            top10Rate: 0.0,
                            winRate: 0.0,
                        },
                        Duo: {
                            tier: "",
                            division: 0,
                            point: 0,
                            top10Rate: 0.0,
                            winRate: 0.0,
                        },
                        Squad: {
                            tier: "",
                            division: 0,
                            point: 0,
                            top10Rate: 0.0,
                            winRate: 0.0,
                        },
                    },
                    MatchHistory: [],
                },
            },
            System: {
                status: "",
                url: "",
            },
        };
    }
    /**
     * Accessing User data
     */
    getUser() {
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
                        PlayerUnknownBattleGrounds: {
                            playerName:
                                data.Account.PlayerUnknownBattleGrounds
                                    .playerName,
                        },
                    },
                })
            );
    }
    /**
     * Handling the form submission
     * @param {Event} event
     */
    handleSubmit(event) {
        const delay = 1800;
        event.preventDefault();
        fetch("/PlayerUnknownBattleGrounds/Players", {
            method: "POST",
            body: JSON.stringify({
                pubgSearch:
                    this.state.Accounts.PlayerUnknownBattleGrounds.search,
            }),
            headers: {
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((data) =>
                this.setState({
                    System: {
                        status: data.status,
                        url: data.url,
                    },
                })
            )
            .then(() => this.redirector(delay));
    }
    /**
     * Redirecting the user to an intended url
     * @param {number} delay
     */
    redirector(delay) {
        setTimeout(() => {
            window.location.href = this.state.System.url;
        }, delay);
    }
    /**
     * Handling any change that is made in the user interface
     * @param {Event} event
     */
    handleChange(event) {
        const target = event.target;
        const value = target.value;
        const name = target.name;
        this.setState((previous) => ({
            ...previous,
            Accounts: {
                ...previous.Accounts,
                PlayerUnknownBattleGrounds: {
                    ...previous.Accounts.PlayerUnknownBattleGrounds,
                    [name]: value,
                },
            },
        }));
    }
    /**
     * Sending a request to the server to update the data in its cache database before refreshing the page
     */
    updateData() {
        const delay = 1000;
        fetch("/PlayerUnknownBattleGrounds/Refresh", {
            method: "POST",
        })
            .then((response) => response.json())
            .then((data) =>
                this.setState({
                    System: {
                        status: data.status,
                        url: data.url,
                    },
                })
            )
            .then(() => this.redirector(delay));
    }
    /**
     * Accessing latest version data
     */
    getVersion() {
        fetch("/PlayerUnknownBattleGrounds/PatchNotes", {
            method: "GET",
        })
            .then((response) => response.json())
            .then((data) =>
                this.setState({
                    Accounts: {
                        PlayerUnknownBattleGrounds: {
                            Version: {
                                major: data.major,
                                minor: data.minor,
                            },
                        },
                    },
                })
            );
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
     * Retrieving data from PUBG Corporations data center for the user
     * @returns {void}
     */
    getPlayerData() {
        fetch("/PlayerUnknownBattleGrounds/Search/Player", {
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
        } else if (killStreak >= 1 && killStreak < 5) {
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
     * Retrieving data from PUBG Corporations data center for the user's current season
     * @returns {void}
     */
    getSeason() {
        fetch("/PlayerUnknownBattleGrounds/CurrentSeason", {
            method: "GET",
        })
            .then((response) => response.json())
            .then((data) =>
                this.setState({
                    Accounts: {
                        PlayerUnknownBattleGrounds: {
                            Season: {
                                Solo: {
                                    tier: data.Season.Solo.tier,
                                    division: data.Season.Solo.division,
                                    point: data.Season.Solo.rankPoint,
                                    winRate: data.Season.Solo.winRate,
                                    top10Rate: data.Season.Solo.top10Rate,
                                },
                                Duo: {
                                    tier: data.Season.Duo.tier,
                                    division: data.Season.Duo.division,
                                    point: data.Season.Duo.rankPoint,
                                    winRate: data.Season.Duo.winRate,
                                    top10Rate: data.Season.Duo.top10Rate,
                                },
                                Squad: {
                                    tier: data.Season.Squad.tier,
                                    division: data.Season.Squad.division,
                                    point: data.Season.Squad.rankPoint,
                                    winRate: data.Season.Squad.winRate,
                                    top10Rate: data.Season.Squad.top10Rate,
                                },
                            },
                        },
                    },
                })
            );
    }
    /**
     * Rendering the rank of the player
     * @param {object} season
     * @returns {string}
     */
    renderRank(season) {
        const mode = this.filterRanks(season);
        switch (mode.tier) {
            case null:
                return "Unranked";
            case "Unranked":
                return mode.tier;
            case "Master":
                return `${mode.tier} - ${mode.point} pts`;
            default:
                return `${mode.tier} ${mode.division} - ${mode.point} pts`;
        }
    }
    /**
     * filtering ranks from highest to lowest to return the highest one
     * @param {object} season
     * @returns {object}
     */
    filterRanks(season) {
        let modes = [season.Solo, season.Duo, season.Squad];
        let tiers = [];
        let highestRankPriority = 0;
        for (let index = 0; index < modes.length; index++) {
            let rankPriority = 0;
            switch (modes[index].tier) {
                case "Unranked":
                    rankPriority = 0;
                    break;
                case "Bronze":
                    rankPriority = 1;
                    break;
                case "Silver":
                    rankPriority = 2;
                    break;
                case "Gold":
                    rankPriority = 3;
                    break;
                case "Platinum":
                    rankPriority = 4;
                    break;
                case "Diamond":
                    rankPriority = 5;
                    break;
                case "Master":
                    rankPriority = 6;
                    break;
            }
            tiers.push(rankPriority);
        }
        highestRankPriority = Math.max(...tiers);
        const index = tiers.indexOf(highestRankPriority);
        return modes[index];
    }
    /**
     * Rendering the rank of the player
     * @param {object} season
     * @returns {object}
     */
    renderRankImage(season) {
        const mode = this.filterRanks(season);
        if (mode.tier == "Unranked" || mode.tier == "Master") {
            return (
                <img
                    src={`/Public/Images/Player Unknown Battle Grounds/Ranks/${mode.tier}.png`}
                />
            );
        } else {
            return (
                <img
                    src={`/Public/Images/Player Unknown Battle Grounds/Ranks/${mode.tier}-${mode.division}.png`}
                />
            );
        }
    }
    /**
     * Rendering the rank of the player
     * @param {object} mode
     * @returns {object}
     */
    renderRankModeImage(mode) {
        if (mode.tier == "Unranked" || mode.tier == "Master") {
            return (
                <img
                    src={`/Public/Images/Player Unknown Battle Grounds/Ranks/${mode.tier}.png`}
                />
            );
        } else {
            return (
                <img
                    src={`/Public/Images/Player Unknown Battle Grounds/Ranks/${mode.tier}-${mode.division}.png`}
                />
            );
        }
    }
    /**
     * Rendering the rank of the player
     * @param {object} mode
     * @returns {string}
     */
    renderRankModeTier(mode) {
        if (mode.tier == "Unranked") {
            return [mode.tier, "", ""];
        } else if (mode.tier != "Master") {
            return [
                `${mode.tier} ${mode.division} - ${mode.point} pts`,
                `${mode.winRate} %`,
                `${mode.top10Rate} %`,
            ];
        } else {
            return [
                `${mode.tier} - ${mode.point} pts`,
                `${mode.winRate} %`,
                `${mode.top10Rate} %`,
            ];
        }
    }
    /**
     * Retrieving data from PUBG Corporations data center for the user's match history
     * @returns {void}
     */
    getMatchHistory() {
        fetch("/PlayerUnknownBattleGrounds/MatchHistory", {
            method: "GET",
        })
            .then((response) => response.json())
            .then((data) =>
                this.setState({
                    Accounts: {
                        PlayerUnknownBattleGrounds: {
                            MatchHistory: data.matches,
                        },
                    },
                })
            );
    }
    /**
     * Checking whether the player has played in this mode
     * @param {string} mode The ranked mode
     * @returns {string}
     */
    verify_rank(mode) {
        if (mode == "Unranked") {
            return "none";
        }
    }
    /**
     * Checking the placement of the player
     * @param   {number}   place    The rank of the player in that particular match
     * @returns {string}
     */
    verifyPlace(place) {
        if (place < 11) {
            if (place == 1) {
                return "rgba(100%, 84.3%, 0%, 50%)";
            } else if (place == 2) {
                return "rgba(50%, 50%, 50%, 50%)";
            } else if (place == 3) {
                return "rgba(69.02%, 55.29%, 34.12%, 50%)";
            } else {
                return "rgba(0%, 100%, 0%, 50%)";
            }
        } else {
            return "rgba(100%, 0%, 0%, 50%)";
        }
    }
    /**
     * Verifying the travelled by the player
     * @param {number} distance
     * @returns {string}
     */
    verifyPlayerUnknownBattleGrounds_distance(distance) {
        if (distance >= 2) {
            return "rgb(0%, 100%, 0%)";
        } else if (distance >= 1 && distance < 2) {
            return "rgb(100%, 100%, 0%)";
        } else {
            return "rgb(100%, 0%, 0%)";
        }
    }
    /**
     * Handling the form submission
     * @param {Event} event
     */
    handleSubmit(event) {
        const delay = 1800;
        event.preventDefault();
        fetch("/PlayersUnknownBattleGrounds/Players", {
            method: "POST",
            body: JSON.stringify({
                pubgSearch:
                    this.state.Accounts.PlayerUnknownBattleGrounds.search,
            }),
            headers: {
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((data) =>
                this.setState({
                    System: {
                        status: data.status,
                        url: data.url,
                    },
                })
            )
            .then(() => this.redirector(delay));
    }
    /**
     * Renders the components that are being returned
     * @returns {object[]}
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
    componentDidMount() {
        this.getUser();
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
                    <div>
                        <form
                            method="POST"
                            onSubmit={this.handleSubmit.bind(this)}
                        >
                            <input
                                type="search"
                                name="search"
                                placeholder="Search..."
                                value={
                                    this.state.Accounts
                                        .PlayerUnknownBattleGrounds.search
                                }
                                onChange={this.handleChange.bind(this)}
                                required
                            />
                            <button>
                                <i class="fa fa-search"></i>
                            </button>
                        </form>
                    </div>
                    <div>
                        <button onClick={this.updateData.bind(this)}>
                            Update
                        </button>
                    </div>
                    <div>
                        <PatchNotes />
                    </div>
                    <div>
                        <a
                            href="https://www.pubgesports.com/en/main"
                            target="__blank"
                        >
                            Esports
                        </a>
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
    componentDidMount() {
        this.getUser();
    }
    render() {
        return (
            <main>
                <nav>
                    <div>{this.verifyUser_username()}</div>
                    <div>
                        <a href="/Sign-Out">
                            <i class="fa fa-sign-out"></i>
                        </a>
                    </div>
                </nav>
                <div>
                    <Player
                        playerName={
                            this.state.Accounts.PlayerUnknownBattleGrounds
                                .playerName
                        }
                    />
                    <div>
                        <Season />
                        <MatchHistory />
                    </div>
                </div>
            </main>
        );
    }
}
/**
 * The component that is the footer
 */
class Footer extends Application {
    constructor(props) {
        super(props);
    }
    render() {
        return (
            <footer>
                <div>Parkinston</div>
                <div>
                    <img src="/Public/Images/Player Unknown Battle Grounds/PUBG RGB Logos (Web)/PUBG_BG_Full_Flat_White_2048.png" />
                </div>
            </footer>
        );
    }
}
/**
 * Patch Notes component
 */
class PatchNotes extends Header {
    constructor(props) {
        super(props);
        this.state = {
            Accounts: {
                PlayerUnknownBattleGrounds: {
                    Version: {
                        major: 0,
                        minor: 0,
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
            <a
                href={`https://na.battlegrounds.pubg.com/patch-notes/patch-notes-update-${this.state.Accounts.PlayerUnknownBattleGrounds.Version.major}-${this.state.Accounts.PlayerUnknownBattleGrounds.Version.minor}/`}
                target="__blank"
            >
                Patch Notes
            </a>
        );
    }
}
/** Player component */
class Player extends Main {
    constructor(props) {
        super(props);
        this.props = {
            playerName: "",
        };
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
        this.getPlayerData();
    }
    render() {
        return (
            <div id="player">
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
                                            .PlayerUnknownBattleGrounds.Player
                                            .Solo.winrate
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
                                            .PlayerUnknownBattleGrounds.Player
                                            .Solo.top10Probability
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
                                            .PlayerUnknownBattleGrounds.Player
                                            .Duo.winrate
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
                                            .PlayerUnknownBattleGrounds.Player
                                            .Duo.top10Probability
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
                                            .PlayerUnknownBattleGrounds.Player
                                            .Squad.winrate
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
                                            .PlayerUnknownBattleGrounds.Player
                                            .Squad.top10Probability
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
                                            .PlayerUnknownBattleGrounds.Player
                                            .kda
                                    ),
                                }}
                            >
                                {
                                    this.state.Accounts
                                        .PlayerUnknownBattleGrounds.Player.kda
                                }
                            </div>
                        </div>
                        <div>
                            <div>Kill Streak:</div>
                            <div
                                style={{
                                    color: this.verifyPlayerUnknownBattleGrounds_killStreak(
                                        this.state.Accounts
                                            .PlayerUnknownBattleGrounds.Player
                                            .killStreak
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
                                            .PlayerUnknownBattleGrounds.Player
                                            .longestKill
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
                                            .PlayerUnknownBattleGrounds.Player
                                            .headshot
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
                                            .PlayerUnknownBattleGrounds.Player
                                            .damagePerMatch
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
                    <HighestRanked />
                </div>
            </div>
        );
    }
}
/**
 * Highest Ranked component
 */
class HighestRanked extends Player {
    constructor(props) {
        super(props);
        this.state = {
            Accounts: {
                PlayerUnknownBattleGrounds: {
                    Season: {
                        Solo: {
                            tier: "",
                            division: 0,
                            point: 0,
                            top10Rate: 0.0,
                            winRate: 0.0,
                        },
                        Duo: {
                            tier: "",
                            division: 0,
                            point: 0,
                            top10Rate: 0.0,
                            winRate: 0.0,
                        },
                        Squad: {
                            tier: "",
                            division: 0,
                            point: 0,
                            top10Rate: 0.0,
                            winRate: 0.0,
                        },
                    },
                },
            },
        };
    }
    componentDidMount() {
        this.getSeason();
    }
    render() {
        return (
            <div id="highestRanked">
                <div>
                    {this.renderRankImage(
                        this.state.Accounts.PlayerUnknownBattleGrounds.Season
                    )}
                </div>
                <div>
                    {this.renderRank(
                        this.state.Accounts.PlayerUnknownBattleGrounds.Season
                    )}
                </div>
            </div>
        );
    }
}
/**
 * The current season component of a player
 */
class Season extends Main {
    constructor(props) {
        super(props);
        this.state = {
            Accounts: {
                PlayerUnknownBattleGrounds: {
                    Season: {
                        Solo: {
                            tier: "",
                            division: 0,
                            point: 0,
                            top10Rate: 0.0,
                            winRate: 0.0,
                        },
                        Duo: {
                            tier: "",
                            division: 0,
                            point: 0,
                            top10Rate: 0.0,
                            winRate: 0.0,
                        },
                        Squad: {
                            tier: "",
                            division: 0,
                            point: 0,
                            top10Rate: 0.0,
                            winRate: 0.0,
                        },
                    },
                },
            },
        };
    }
    componentDidMount() {
        this.getSeason();
    }
    render() {
        return (
            <div id="currentSeason">
                <div id="solo">
                    <div>Solo</div>
                    <div>
                        {this.renderRankModeImage(
                            this.state.Accounts.PlayerUnknownBattleGrounds
                                .Season.Solo
                        )}
                    </div>
                    <div>
                        {
                            this.renderRankModeTier(
                                this.state.Accounts.PlayerUnknownBattleGrounds
                                    .Season.Solo
                            )[0]
                        }
                    </div>
                    <div
                        style={{
                            display: this.verify_rank(
                                this.state.Accounts.PlayerUnknownBattleGrounds
                                    .Season.Solo.tier
                            ),
                        }}
                    >
                        <div>Win Rate:</div>
                        <div
                            style={{
                                color: this.verifyPlayerUnknownBattleGrounds_winRate(
                                    this.state.Accounts
                                        .PlayerUnknownBattleGrounds.Season.Solo
                                        .winRate
                                ),
                            }}
                        >
                            {
                                this.renderRankModeTier(
                                    this.state.Accounts
                                        .PlayerUnknownBattleGrounds.Season.Solo
                                )[1]
                            }
                        </div>
                    </div>
                    <div
                        style={{
                            display: this.verify_rank(
                                this.state.Accounts.PlayerUnknownBattleGrounds
                                    .Season.Solo.tier
                            ),
                        }}
                    >
                        <div>Top 10:</div>
                        <div
                            style={{
                                color: this.verifyPlayerUnknownBattleGrounds_top10Probability(
                                    this.state.Accounts
                                        .PlayerUnknownBattleGrounds.Season.Solo
                                        .top10Rate
                                ),
                            }}
                        >
                            {
                                this.renderRankModeTier(
                                    this.state.Accounts
                                        .PlayerUnknownBattleGrounds.Season.Solo
                                )[2]
                            }
                        </div>
                    </div>
                </div>
                <div id="duo">
                    <div>Duo</div>
                    <div>
                        {this.renderRankModeImage(
                            this.state.Accounts.PlayerUnknownBattleGrounds
                                .Season.Duo
                        )}
                    </div>
                    <div>
                        {
                            this.renderRankModeTier(
                                this.state.Accounts.PlayerUnknownBattleGrounds
                                    .Season.Duo
                            )[0]
                        }
                    </div>
                    <div
                        style={{
                            display: this.verify_rank(
                                this.state.Accounts.PlayerUnknownBattleGrounds
                                    .Season.Duo.tier
                            ),
                        }}
                    >
                        <div>Win Rate:</div>
                        <div
                            style={{
                                color: this.verifyPlayerUnknownBattleGrounds_winRate(
                                    this.state.Accounts
                                        .PlayerUnknownBattleGrounds.Season.Duo
                                        .winRate
                                ),
                            }}
                        >
                            {
                                this.renderRankModeTier(
                                    this.state.Accounts
                                        .PlayerUnknownBattleGrounds.Season.Duo
                                )[1]
                            }
                        </div>
                    </div>
                    <div
                        style={{
                            display: this.verify_rank(
                                this.state.Accounts.PlayerUnknownBattleGrounds
                                    .Season.Duo.tier
                            ),
                        }}
                    >
                        <div>Top 10:</div>
                        <div
                            style={{
                                color: this.verifyPlayerUnknownBattleGrounds_top10Probability(
                                    this.state.Accounts
                                        .PlayerUnknownBattleGrounds.Season.Duo
                                        .top10Rate
                                ),
                            }}
                        >
                            {
                                this.renderRankModeTier(
                                    this.state.Accounts
                                        .PlayerUnknownBattleGrounds.Season.Duo
                                )[2]
                            }
                        </div>
                    </div>
                </div>
                <div id="squad">
                    <div>Squad</div>
                    <div>
                        {this.renderRankModeImage(
                            this.state.Accounts.PlayerUnknownBattleGrounds
                                .Season.Squad
                        )}
                    </div>
                    <div>
                        {
                            this.renderRankModeTier(
                                this.state.Accounts.PlayerUnknownBattleGrounds
                                    .Season.Squad
                            )[0]
                        }
                    </div>
                    <div
                        style={{
                            display: this.verify_rank(
                                this.state.Accounts.PlayerUnknownBattleGrounds
                                    .Season.Squad.tier
                            ),
                        }}
                    >
                        <div>Win Rate:</div>
                        <div
                            style={{
                                color: this.verifyPlayerUnknownBattleGrounds_winRate(
                                    this.state.Accounts
                                        .PlayerUnknownBattleGrounds.Season.Squad
                                        .winRate
                                ),
                            }}
                        >
                            {
                                this.renderRankModeTier(
                                    this.state.Accounts
                                        .PlayerUnknownBattleGrounds.Season.Squad
                                )[1]
                            }
                        </div>
                    </div>
                    <div
                        style={{
                            display: this.verify_rank(
                                this.state.Accounts.PlayerUnknownBattleGrounds
                                    .Season.Squad.tier
                            ),
                        }}
                    >
                        <div>Top 10:</div>
                        <div
                            style={{
                                color: this.verifyPlayerUnknownBattleGrounds_top10Probability(
                                    this.state.Accounts
                                        .PlayerUnknownBattleGrounds.Season.Squad
                                        .top10Rate
                                ),
                            }}
                        >
                            {
                                this.renderRankModeTier(
                                    this.state.Accounts
                                        .PlayerUnknownBattleGrounds.Season.Squad
                                )[2]
                            }
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}
/**
 * Match History Component
 */
class MatchHistory extends Main {
    constructor(props) {
        super(props);
        this.state = {
            Accounts: {
                PlayerUnknownBattleGrounds: {
                    MatchHistory: [],
                },
            },
        };
    }
    componentDidMount() {
        this.getMatchHistory();
    }
    render() {
        return (
            <div id="matchHistory">
                {this.state.Accounts.PlayerUnknownBattleGrounds.MatchHistory.map(
                    (match) => {
                        return (
                            <div
                                style={{
                                    backgroundColor: this.verifyPlace(
                                        match.rank
                                    ),
                                }}
                            >
                                <div>{match.rank}</div>
                                <div
                                    style={{
                                        color: this.verifyPlayerUnknownBattleGrounds_killStreak(
                                            match.kill
                                        ),
                                    }}
                                >{`${match.kill} Kills`}</div>
                                <div
                                    style={{
                                        color: this.verifyPlayerUnknownBattleGrounds_damagePerMatch(
                                            match.damage
                                        ),
                                    }}
                                >{`${match.damage} Damage`}</div>
                                <div
                                    style={{
                                        color: this.verifyPlayerUnknownBattleGrounds_distance(
                                            match.distance
                                        ),
                                    }}
                                >{`${match.distance} Km`}</div>
                            </div>
                        );
                    }
                )}
            </div>
        );
    }
}
// Rendering the page
ReactDOM.render(<Application />, document.body);
