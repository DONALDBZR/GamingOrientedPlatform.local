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
                            gameName: data.Account.LeagueOfLegends.gameName,
                        },
                    },
                })
            );
    }
    /**
     * Accessing Summoner Data
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
                                    summonerIcon: data.profileIconId,
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
                                    summonerIcon: data.profileIconId,
                                    kdaRatio: data.kdaRatio,
                                    csMin: data.csMin,
                                    vsMin: data.vsMin,
                                    SoloDuo: {
                                        tier: data.soloDuoTier,
                                        division: data.soloDuoRank,
                                        leaguePoints: data.soloDuoLeaguePoints,
                                        winRate: data.soloDuoWinRate,
                                    },
                                    Flex5v5: {
                                        tier: data.flexTier,
                                        division: data.flexRank,
                                        leaguePoints: data.flexLeaguePoints,
                                        winRate: data.flexWinRate,
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
            return (
                <DataDragon
                    dataSet="item"
                    id={item}
                    major={this.props.major}
                    minor={this.props.minor}
                    patchNotes={this.props.patchNotes}
                />
            );
        } else {
            return null;
        }
    }
    /**
     * Redirecting the user to an intended url
     * @param {int} delay
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
            return "/Public/Images/Ranks/Emblem_Unranked.png";
        } else {
            return `/Public/Images/Ranks/Emblem_${tier}.png`;
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
                                    name="lolSearch"
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
        this.getVersion();
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
                            major={
                                this.state.Accounts.LeagueOfLegends.Version
                                    .major
                            }
                            minor={
                                this.state.Accounts.LeagueOfLegends.Version
                                    .minor
                            }
                            patchNotes={
                                this.state.Accounts.LeagueOfLegends.Version
                                    .patchNotes
                            }
                        />
                        <div>
                            <ChampionMastery
                                major={
                                    this.state.Accounts.LeagueOfLegends.Version
                                        .major
                                }
                                minor={
                                    this.state.Accounts.LeagueOfLegends.Version
                                        .minor
                                }
                                patchNotes={
                                    this.state.Accounts.LeagueOfLegends.Version
                                        .patchNotes
                                }
                            />
                            <MatchHistory
                                major={
                                    this.state.Accounts.LeagueOfLegends.Version
                                        .major
                                }
                                minor={
                                    this.state.Accounts.LeagueOfLegends.Version
                                        .minor
                                }
                                patchNotes={
                                    this.state.Accounts.LeagueOfLegends.Version
                                        .patchNotes
                                }
                            />
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
                    href={`https://www.leagueoflegends.com/en-us/news/game-updates/patch-${this.state.majorVersion}-${this.state.minorVersion}-notes/`}
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
                        major={this.props.major}
                        minor={this.props.minor}
                        patchNotes={this.props.patchNotes}
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
class DataDragon extends Main {
    constructor(props) {
        super(props);
    }
    render() {
        return (
            <img
                src={`http://ddragon.leagueoflegends.com/cdn/${this.props.major}.${this.props.minor}.${this.props.patchNotes}/img/${this.props.dataSet}/${this.props.id}.png`}
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
                    <div>Maintenance</div>
                    <div>
                        {this.verifyLeagueOfLegends_platformStatus_maintenance()}
                    </div>
                </div>
                <div>
                    <div>Incidents</div>
                    {this.state.Accounts.LeagueOfLegends.PlatformStatus.incidents.map(
                        (incident) => {
                            return (
                                <div>
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
                                        major={this.props.major}
                                        minor={this.props.minor}
                                        patchNotes={this.props.patchNotes}
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
                                        major={this.props.major}
                                        minor={this.props.minor}
                                        patchNotes={this.props.patchNotes}
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
