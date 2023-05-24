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
                LeagueOfLegends: {
                    gameName: "",
                    search: "",
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
                    PlatformStatus: {
                        maintenance: [],
                        incidents: [],
                    },
                    matchHistories: [],
                    championMasteries: [],
                    Version: {
                        major: 0,
                        minor: 0,
                        patchNotes: 0,
                    },
                },
            },
            System: {
                url: "",
                status: 0,
            },
        };
    }
    /**
     * Accessing User data
     * @returns {void}
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
                        LeagueOfLegends: {
                            gameName: data.Search.LeagueOfLegends.gameName,
                        },
                    },
                })
            );
    }
    /**
     * Accessing Summoner Data
     * @returns {void}
     */
    getSummoner() {
        if (window.location.pathname.includes("Home")) {
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
        } else {
            fetch("/LeagueOfLegends/Search/Summoner", {
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
    }
    /**
     * Accessing latest version data
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
     * Retrieving data from Riot Games data center for the user's match history
     * @returns {void}
     */
    getMatchHistories() {
        fetch("/LeagueOfLegends/MatchHistories", {
            method: "GET",
        })
            .then((response) => response.json())
            .then((data) =>
                this.setState({
                    Accounts: {
                        LeagueOfLegends: {
                            matchHistories: data.matchHistories,
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
     * Verifying the CS before styling it
     * @param {number} cs
     * @param {number} min
     * @returns {string}
     */
    verifyLeagueOfLegends_cs(cs, min) {
        if (cs >= 6 * min) {
            return "rgb(0%, 100%, 0%)";
        } else if (cs >= 1 * min && cs < 6 * min) {
            return "rgb(100%, 100%, 0%)";
        } else {
            return "rgb(100%, 0%, 0%)";
        }
    }
    /**
     * Verifying the VS/Min before styling it
     * @param {number} vs
     * @param {number} min
     * @returns {string}
     */
    verifyLeagueOfLegends_vs(vs, min) {
        if (vs >= 2 * min) {
            return "rgb(0%, 100%, 0%)";
        } else if (vs >= 1 * min && vs < 2 * min) {
            return "rgb(100%, 100%, 0%)";
        } else {
            return "rgb(100%, 0%, 0%)";
        }
    }
    /**
     * Verifying that the player has won his/her match before styling it
     * @param {boolean} win
     * @param {number} length
     * @returns {string}
     */
    verifyLeagueOfLegends_win(win, length) {
        if (win && length > 3 * 60) {
            return "rgba(0%, 100%, 0%, 50%)";
        } else if (!win && length > 3 * 60) {
            return "rgba(100%, 0%, 0%, 50%)";
        } else {
            return "rgba(50%, 50%, 50%, 100%)";
        }
    }
    /**
     * Verifying that the player has bought this item before rendering it
     * @param {number} item
     * @returns {object | null}
     */
    verifyLeagueOfLegends_item(item) {
        if (item != 0) {
            return <DataDragon dataSet="item" id={item} />;
        } else {
            return null;
        }
    }
    /**
     * Redirecting the user to an intended url
     * @param {number} delay
     * @returns {void}
     */
    redirector(delay) {
        setTimeout(() => {
            window.location.href = this.state.System.url;
        }, delay);
    }
    /**
     * Handling any change that is made in the user interface
     * @param {Event} event
     * @returns {void}
     */
    handleChange(event) {
        const target = event.target;
        const value = target.value;
        const name = target.name;
        this.setState((previous) => ({
            ...previous,
            Accounts: {
                ...previous.Accounts,
                LeagueOfLegends: {
                    ...previous.Accounts.LeagueOfLegends,
                    [name]: value,
                },
            },
        }));
    }
    /**
     * Handling the form submission
     * @param {Event} event
     * @returns {void}
     */
    handleSubmit(event) {
        const delay = 1800;
        event.preventDefault();
        fetch("/LeagueOfLegends/Summoners", {
            method: "POST",
            body: JSON.stringify({
                lolSearch: this.state.Accounts.LeagueOfLegends.search,
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
     * Sending a request to the server to update the data in its cache database before refreshing the page
     * @returns {void}
     */
    updateData() {
        const delay = 1000;
        fetch("/LeagueOfLegends/Refresh", {
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
     * Retrieving data from Riot Games data center for the user's champion's mastery
     * @returns {void}
     */
    getChampionMastery() {
        fetch("/LeagueOfLegends/ChampionMastery", {
            method: "GET",
        })
            .then((response) => response.json())
            .then((data) =>
                this.setState({
                    Accounts: {
                        LeagueOfLegends: {
                            championMasteries: data.championMasteries,
                        },
                    },
                })
            );
    }
    /**
     * Handling the level that is retrieved from the data
     * @param {number} champion_level
     * @returns {number | string}
     */
    verifyLeagueOfLegends_championMastery_level(champion_level) {
        if (champion_level >= 4) {
            return champion_level;
        } else {
            return "default";
        }
    }
    /**
     * Retrieving data from Riot Games data center for the status of the platform
     * @returns {void}
     */
    getPlatformStatus() {
        fetch("/LeagueOfLegends/PlatformStatus", {
            method: "GET",
        })
            .then((response) => response.json())
            .then((data) =>
                this.setState({
                    Accounts: {
                        LeagueOfLegends: {
                            PlatformStatus: {
                                maintenance: data.maintenance,
                                incidents: data.incidents,
                            },
                        },
                    },
                })
            );
    }
    /**
     * Handling the maintenance that is retrieved from the data
     * @returns {string}
     */
    verifyLeagueOfLegends_platformStatus_maintenance() {
        if (
            this.state.Accounts.LeagueOfLegends.PlatformStatus.maintenance > 0
        ) {
            return this.state.Accounts.LeagueOfLegends.PlatformStatus
                .maintenance[0].content;
        } else {
            return "None";
        }
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
     * @param {string | null} tier
     * @returns {string}
     */
    verifyLeagueOfLegends_rank_emblem(tier) {
        if (tier == "" || tier == null) {
            return "/Public/Images/Riot Games/Ranks/Emblem_Unranked.png";
        } else {
            return `/Public/Images/Riot Games/Ranks/Emblem_${tier}.png`;
        }
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
        if (document.body.clientWidth <= 639) {
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
                                        this.state.Accounts.LeagueOfLegends
                                            .search
                                    }
                                    onChange={this.handleChange.bind(this)}
                                    required
                                />
                                <button class="fa fa-search"></button>
                            </form>
                        </div>
                        <div>
                            <button
                                onClick={this.updateData.bind(this)}
                                class="fa fa-refresh"
                            ></button>
                        </div>
                        <div>
                            <a
                                href="https://www.proguides.com/leagueoflegends/champions/search"
                                target="__blank"
                                class="fa-solid fa-ranking-star"
                            ></a>
                        </div>
                        <div>
                            <PatchNotes />
                        </div>
                        <div>
                            <a
                                href="https://lolesports.com/"
                                target="__blank"
                                class="fa-solid fa-trophy"
                            ></a>
                        </div>
                    </nav>
                </header>
            );
        } else {
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
                                        this.state.Accounts.LeagueOfLegends
                                            .search
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
                            <a
                                href="https://www.proguides.com/leagueoflegends/champions/search"
                                target="__blank"
                            >
                                Meta
                            </a>
                        </div>
                        <div>
                            <PatchNotes />
                        </div>
                        <div>
                            <a href="https://lolesports.com/" target="__blank">
                                Esports
                            </a>
                        </div>
                    </nav>
                </header>
            );
        }
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
        if (document.body.clientWidth >= 640) {
            this.getUser();
        }
    }
    render() {
        if (document.body.clientWidth <= 639) {
            return (
                <main>
                    <div>
                        <Summoner
                            gameName={
                                this.state.Accounts.LeagueOfLegends.gameName
                            }
                        />
                        <div>
                            <ChampionMastery />
                            <MatchHistory />
                        </div>
                    </div>
                </main>
            );
        } else {
            return (
                <main>
                    <nav>
                        <div>{this.verifyUser_username()}</div>
                        <div>
                            <a href="/Sign-Out" class="fa fa-sign-out"></a>
                        </div>
                    </nav>
                    <div>
                        <Summoner
                            gameName={
                                this.state.Accounts.LeagueOfLegends.gameName
                            }
                        />
                        <div>
                            <ChampionMastery />
                            <MatchHistory />
                        </div>
                    </div>
                </main>
            );
        }
    }
}
/**
 * The component that is the footer
 */
class Footer extends Application {
    constructor(props) {
        super(props);
    }
    /**
     * Methods to be run as soon as the component is mounted
     */
    componentDidMount() {
        if (document.body.clientWidth <= 639) {
            this.getUser();
        }
    }
    render() {
        if (document.body.clientWidth <= 639) {
            return (
                <footer>
                    <nav>
                        <div>{this.verifyUser_username()}</div>
                        <div>
                            <a href="/Sign-Out" class="fa fa-sign-out"></a>
                        </div>
                    </nav>
                    <div>Parkinston</div>
                    <div>
                        <img src="/Public/Images/Riot Games RGB Logos (Web)/001_RG 2021 Logomark/001.1_RG_2021_LOGOMARK_BLACK.png" />
                    </div>
                </footer>
            );
        } else {
            return (
                <footer>
                    <div>Parkinston</div>
                    <div>
                        <img src="/Public/Images/Riot Games RGB Logos (Web)/001_RG 2021 Logomark/001.1_RG_2021_LOGOMARK_BLACK.png" />
                    </div>
                </footer>
            );
        }
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
        if (document.body.clientWidth <= 639) {
            return (
                <a
                    href={`https://www.leagueoflegends.com/en-us/news/game-updates/patch-${this.state.Accounts.LeagueOfLegends.Version.major}-${this.state.Accounts.LeagueOfLegends.Version.minor}-notes/`}
                    target="__blank"
                    class="fas fa-sticky-note"
                ></a>
            );
        } else {
            return (
                <a
                    href={`https://www.leagueoflegends.com/en-us/news/game-updates/patch-${this.state.Accounts.LeagueOfLegends.Version.major}-${this.state.Accounts.LeagueOfLegends.Version.minor}-notes/`}
                    target="__blank"
                >
                    Patch Notes
                </a>
            );
        }
    }
}
/** Summoner component */
class Summoner extends Main {
    constructor(props) {
        super(props);
        this.state = {
            Accounts: {
                LeagueOfLegends: {
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
                },
            },
        };
    }
    componentDidMount() {
        this.getSummoner();
    }
    render() {
        return (
            <header>
                <div>
                    <DataDragon
                        dataSet="profileicon"
                        id={
                            this.state.Accounts.LeagueOfLegends.Summoner
                                .profileIconId
                        }
                    />
                    <div>
                        Level{" "}
                        {this.state.Accounts.LeagueOfLegends.Summoner.level}
                    </div>
                    <div>{this.props.gameName}</div>
                </div>
                <div>
                    <div>
                        <div>Solo/Duo</div>
                        <div>
                            <img
                                src={this.verifyLeagueOfLegends_rank_emblem(
                                    this.state.Accounts.LeagueOfLegends.Summoner
                                        .SoloDuo.tier
                                )}
                            />
                        </div>
                        <div>
                            <div>
                                {this.verifyLeagueOfLegends_rank(
                                    this.state.Accounts.LeagueOfLegends.Summoner
                                        .SoloDuo
                                )}
                            </div>
                            <div>Win Rate:</div>
                            <div
                                style={{
                                    color: this.verifyLeagueOfLegends_winRate(
                                        this.state.Accounts.LeagueOfLegends
                                            .Summoner.SoloDuo.winRate
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
                                    this.state.Accounts.LeagueOfLegends.Summoner
                                        .Flex5v5.tier
                                )}
                            />
                        </div>
                        <div>
                            <div>
                                {this.verifyLeagueOfLegends_rank(
                                    this.state.Accounts.LeagueOfLegends.Summoner
                                        .Flex5v5
                                )}
                            </div>
                            <div>Win Rate:</div>
                            <div
                                style={{
                                    color: this.verifyLeagueOfLegends_winRate(
                                        this.state.Accounts.LeagueOfLegends
                                            .Summoner.Flex5v5.winRate
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
                                    this.state.Accounts.LeagueOfLegends.Summoner
                                        .kda
                                ),
                            }}
                        >
                            {this.state.Accounts.LeagueOfLegends.Summoner.kda}
                        </div>
                    </div>
                    <div>
                        <div>CS/Min:</div>
                        <div
                            style={{
                                color: this.verifyLeagueOfLegends_csMin(
                                    this.state.Accounts.LeagueOfLegends.Summoner
                                        .csMin
                                ),
                            }}
                        >
                            {this.state.Accounts.LeagueOfLegends.Summoner.csMin}
                        </div>
                    </div>
                    <div>
                        <div>VS/Min:</div>
                        <div
                            style={{
                                color: this.verifyLeagueOfLegends_vsMin(
                                    this.state.Accounts.LeagueOfLegends.Summoner
                                        .vsMin
                                ),
                            }}
                        >
                            {this.state.Accounts.LeagueOfLegends.Summoner.vsMin}
                        </div>
                    </div>
                </div>
                <PlatformStatus />
            </header>
        );
    }
}
/**
 * Data Dragon component
 */
class DataDragon extends Summoner {
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
                src={`http://ddragon.leagueoflegends.com/cdn/${this.state.Accounts.LeagueOfLegends.Version.major}.${this.state.Accounts.LeagueOfLegends.Version.minor}.${this.state.Accounts.LeagueOfLegends.Version.patchNotes}/img/${this.props.dataSet}/${this.props.id}.png`}
            />
        );
    }
}
/**
 * Platform status component
 */
class PlatformStatus extends Main {
    constructor(props) {
        super(props);
        this.state = {
            Accounts: {
                LeagueOfLegends: {
                    PlatformStatus: {
                        maintenance: [],
                        incidents: [],
                    },
                },
            },
        };
    }
    componentDidMount() {
        this.getPlatformStatus();
    }
    render() {
        return (
            <div>
                <div>
                    <div class="platformStatus_title">Maintenance</div>
                    <div>
                        {this.verifyLeagueOfLegends_platformStatus_maintenance()}
                    </div>
                </div>
                <div>
                    <div class="platformStatus_title">Incidents</div>
                    {this.state.Accounts.LeagueOfLegends.PlatformStatus.incidents.map(
                        (incident) => {
                            return (
                                <div class="incident">
                                    <div>{`${incident.title}:`}</div>
                                    <div>{incident.content}</div>
                                </div>
                            );
                        }
                    )}
                </div>
            </div>
        );
    }
}
/**
 * Champion Mastery component
 */
class ChampionMastery extends Main {
    constructor(props) {
        super(props);
        this.state = {
            Accounts: {
                LeagueOfLegends: {
                    championMasteries: [],
                },
            },
        };
    }
    componentDidMount() {
        this.getChampionMastery();
    }
    render() {
        return (
            <div id="championMastery">
                {this.state.Accounts.LeagueOfLegends.championMasteries.map(
                    (championMastery) => {
                        return (
                            <div>
                                <div>
                                    <DataDragon
                                        dataSet="champion"
                                        id={championMastery.id}
                                    />
                                </div>
                                <div>
                                    <div>
                                        <img
                                            src={`https://raw.communitydragon.org/latest/game/assets/ux/mastery/mastery_icon_${this.verifyLeagueOfLegends_championMastery_level(
                                                championMastery.level
                                            )}.png`}
                                        />
                                    </div>
                                    <div>{`${championMastery.points} pts`}</div>
                                </div>
                            </div>
                        );
                    }
                )}
            </div>
        );
    }
}
/**
 * Match History component
 */
class MatchHistory extends Main {
    constructor(props) {
        super(props);
        this.state = {
            Accounts: {
                LeagueOfLegends: {
                    matchHistories: [],
                },
            },
        };
    }
    componentDidMount() {
        this.getMatchHistories();
    }
    render() {
        return (
            <div id="matchHistory">
                {this.state.Accounts.LeagueOfLegends.matchHistories.map(
                    (match) => {
                        return (
                            <div
                                style={{
                                    backgroundColor:
                                        this.verifyLeagueOfLegends_win(
                                            match.win,
                                            match.matchLength
                                        ),
                                }}
                            >
                                <div>
                                    <DataDragon
                                        dataSet="champion"
                                        id={match.champion}
                                    />
                                </div>
                                <div>
                                    <div>
                                        <div>
                                            <div>{`${match.kill}/${match.death}/${match.assist}`}</div>
                                            <div
                                                style={{
                                                    color: this.verifyLeagueOfLegends_kda(
                                                        match.kda
                                                    ),
                                                }}
                                            >
                                                {match.kda}
                                            </div>
                                        </div>
                                        <div
                                            style={{
                                                color: this.verifyLeagueOfLegends_cs(
                                                    match.creepScore,
                                                    match.matchLength / 60
                                                ),
                                            }}
                                        >
                                            {match.creepScore}
                                        </div>
                                        <div
                                            style={{
                                                color: this.verifyLeagueOfLegends_vs(
                                                    match.visualScore,
                                                    match.matchLength / 60
                                                ),
                                            }}
                                        >
                                            {match.visualScore}
                                        </div>
                                        <div>{match.length}</div>
                                        <div>{match.lane}</div>
                                    </div>
                                    <div>
                                        {match.items.map((item) => {
                                            return (
                                                <div>
                                                    {this.verifyLeagueOfLegends_item(
                                                        item
                                                    )}
                                                </div>
                                            );
                                        })}
                                    </div>
                                </div>
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
