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
            /**
             * Username of the user
             * @type {string}
             */
            username: "",
            /**
             * Mail Address of the user
             * @type {string}
             */
            mailAddress: "",
            /**
             * Domain of the application
             * @type {string}
             */
            domain: "",
            /**
             * User's profile picture
             * @type {string}
             */
            profilePicture: "",
            /**
             * User's League of Legends username
             * @type {string}
             */
            lolUsername: "",
            /**
             * User's League of Legends Region
             * @type {string}
             */
            lolRegion: "",
            /**
             * User's Riot's ID
             * @type {string}
             */
            riotId: "",
            /**
             * Summoner's level
             * @type {int}
             */
            level: 0,
            /**
             * Summoner Icon
             * @type {int}
             */
            summonerIcon: 0,
            /**
             * Ranked Solo/Duo rank tier
             * @type {string}
             */
            soloDuoTier: "",
            /**
             * Ranked Solo/Duo rank division
             * @type {string}
             */
            soloDuoDivision: "",
            /**
             * Ranked Solo/Duo rank league points
             * @type {int}
             */
            soloDuoLeaguePoints: 0,
            /**
             * Ranked Solo/Duo rank win rate
             * @type {float}
             */
            soloDuoWinRate: 0.0,
            /**
             * Ranked Flex rank tier
             * @type {string}
             */
            flexTier: "",
            /**
             * Ranked Flex rank division
             * @type {string}
             */
            flexDivision: "",
            /**
             * Ranked Flex rank league points
             * @type {int}
             */
            flexLeaguePoints: 0,
            /**
             * Ranked Flex rank win rate
             * @type {float}
             */
            flexWinRate: 0.0,
            /**
             * KDA Ratio of the player
             * @type {float}
             */
            kdaRatio: 0.0,
            /**
             * Creep Score per minute of the player
             * @type {float}
             */
            csMin: 0.0,
            /**
             * Vision Score per minute of the player
             * @type {float}
             */
            vsMin: 0.0,
            /**
             * Match history of the player
             * @type {array}
             */
            matchHistory: [],
        };
    }
    /**
     * Retrieving the session's data that is stored as a JSON to be used in the rendering
     */
    retrieveSessionData() {
        fetch("/Users/CurrentUser",
            {
                method: "GET"
            })
            .then((response) => response.json())
            .then((data) => this.setState({
                username: data.User.username,
                mailAddress: data.User.mailAddress,
                domain: data.User.domain,
                profilePicture: data.User.profilePicture,
                lolUsername: data.Account.LeagueOfLegends.gameName,
                lolRegion: data.Account.LeagueOfLegends.tagLine,
                riotId: data.Account.LeagueOfLegends.playerUniversallyUniqueIdentifier,
            }));
    }
    /**
     * Retrieving data from Riot Games data center for the user
     */
    retrieveLoL_SummonerData() {
        if (window.location.pathname.includes("Home")) {
            fetch("/LegendsOfLegends/CurrentSummoner",
                {
                    method: "GET"
                })
                .then((response) => response.json())
                .then((data) => this.setState({
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
                }));
        } else {
            fetch("/LegendsOfLegends/Search/Summoner",
                {
                    method: "GET"
                })
                .then((response) => response.json())
                .then((data) => this.setState({
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
                }));
        }
    }
    /**
     * Retrieving data from Riot Games data center for the user's match history
     */
    retrieveLoL_SummonerData_matchHistories() {
        fetch("/LegendsOfLegends/MatchHistories",
            {
                method: "GET"
            })
            .then((response) => response.json())
            .then((data) => this.setState({
                matchHistory: data.MatchHistory,
            }));
    }
    /**
     * Verifying the state before rendering the link
     * @returns {Application} Component
     */
    verifyUser_username() {
        if (this.state.profilePicture != null) {
            return (
                <a href={`/Users/Profile/${this.state.username}`}>
                    <img src={this.state.profilePicture} />
                </a>
            );
        } else {
            return <a href={`/Users/Profile/${this.state.username}`} class="fa fa-user"></a>
        }
    }
    /**
     * Verifying the winrate before styling it
     * @param {float} win_rate
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
     * @param {float} kda
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
     * @param {float} cs_min
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
     * @param {float} vs_min
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
     * @param {float} cs
     * @param {float} min
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
     * @param {float} vs
     * @param {float} min
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
     * @returns {string}
     */
    verifyLeagueOfLegends_win(win) {
        if (win) {
            return "rgba(0%, 100%, 0%, 50%)";
        } else {
            return "rgba(100%, 0%, 0%, 50%)";
        }
    }
    /**
     * Verifying that the player has bought this item before rendering it
     * @param {int} item
     * @returns {string | null}
     */
    verifyLeagueOfLegends_item(item) {
        if (item != 0) {
            return <img src={`https://ddragon.leagueoflegends.com/cdn/12.23.1/img/item/${item}.png`} />;
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
            window.location.href = this.state.url;
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
        this.setState({
            [name]: value,
        });
    }
    /**
     * Handling the form submission
     * @param {Event} event
     */
    handleSubmit(event) {
        const delay = 1800;
        event.preventDefault();
        fetch("/Controllers/LeagueOfLegendsHome.php", {
            method: "POST",
            body: JSON.stringify({
                lolSearch: this.state.lolSearch,
            }),
            headers: {
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((data) =>
                this.setState({
                    status: data.status,
                    message: data.message,
                    url: data.url,
                })
            )
            .then(() => this.redirector(delay));
    }
    /**
     * Handling the response from the server
     * @returns {string}
     */
    handleResponseColor() {
        if (this.state.status == 0) {
            return "rgb(0%, 100%, 0%)";
        } else {
            return "rgb(100%, 0%, 0%)";
        }
    }
    /**
     * Handling the response from the server
     * @returns {string}
     */
    handleResponseFontSize() {
        if (this.state.status == 0) {
            return "71%";
        } else {
            return "180%";
        }
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
    render() {
        return (
            <header>
                <nav>
                    <div>
                        <a href={`/Users/Home/${this.state.username}`}>Parkinston</a>
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
        this.retrieveLoL_SummonerData();
        this.retrieveLoL_SummonerData_matchHistories();
    }
    render() {
        return (
            <main>
                <header>
                    <div>
                        <img src={`http://ddragon.leagueoflegends.com/cdn/12.22.1/img/profileicon/${this.state.summonerIcon}.png`} />
                        <div>Level {this.state.level}</div>
                    </div>
                    <div>
                        <div>
                            <div>Solo/Duo</div>
                            <div>
                                <img src={`/Public/Images/Ranks/Emblem_${this.state.soloDuoTier}.png`} />
                            </div>
                            <div>{`${this.state.soloDuoTier} ${this.state.soloDuoDivision} - ${this.state.soloDuoLeaguePoints} LP`}</div>
                            <div>Win Rate:</div>
                            <div style={{ color: this.verifyLeagueOfLegends_winRate(this.state.soloDuoWinRate) }}>{`${this.state.soloDuoWinRate} %`}</div>
                        </div>
                        <div>
                            <div>Flex 5v5</div>
                            <div>
                                <img src={`/Public/Images/Ranks/Emblem_${this.state.flexTier}.png`} />
                            </div>
                            <div>{`${this.state.flexTier} ${this.state.flexDivision} - ${this.state.flexLeaguePoints} LP`}</div>
                            <div>Win Rate:</div>
                            <div style={{ color: this.verifyLeagueOfLegends_winRate(this.state.flexWinRate) }}>{`${this.state.flexWinRate} %`}</div>
                        </div>
                    </div>
                    <div>
                        <div>
                            <div>KDA:</div>
                            <div style={{ color: this.verifyLeagueOfLegends_kda(this.state.kdaRatio) }}>{this.state.kdaRatio}</div>
                        </div>
                        <div>
                            <div>CS/Min:</div>
                            <div style={{ color: this.verifyLeagueOfLegends_csMin(this.state.csMin) }}>{this.state.csMin}</div>
                        </div>
                        <div>
                            <div>VS/Min:</div>
                            <div style={{ color: this.verifyLeagueOfLegends_vsMin(this.state.vsMin) }}>{this.state.vsMin}</div>
                        </div>
                    </div>
                    <div>
                        <form method="POST" onSubmit={this.handleSubmit.bind(this)}>
                            <input
                                type="search"
                                name="lolSearch"
                                placeholder="Search..."
                                value={this.state.lolSearch}
                                onChange={this.handleChange.bind(this)}
                                required
                            />
                            <button class="fa fa-search"></button>
                        </form>
                    </div>
                </header>
                <div id="matchHistory">
                    {this.state.matchHistory.map((match) => {
                        return (
                            <div style={{ backgroundColor: this.verifyLeagueOfLegends_win(match.win) }}>
                                <div>
                                    <img src={`http://ddragon.leagueoflegends.com/cdn/12.23.1/img/champion/${match.champion}.png`} />
                                </div>
                                <div>
                                    <div>
                                        <div>
                                            <div>{`${match.kill}/${match.death}/${match.assist}`}</div>
                                            <div style={{ color: this.verifyLeagueOfLegends_kda(match.kda) }}>{match.kda}</div>
                                        </div>
                                        <div style={{ color: this.verifyLeagueOfLegends_cs(match.creepScore, match.matchLength / 60) }}>{match.creepScore}</div>
                                        <div style={{ color: this.verifyLeagueOfLegends_vs(match.visualScore, match.matchLength / 60) }}>{match.visualScore}</div>
                                        <div>{match.length}</div>
                                        <div>{match.lane}</div>
                                    </div>
                                    <div>
                                        <div>
                                            {this.verifyLeagueOfLegends_item(match.item0)}
                                        </div>
                                        <div>
                                            {this.verifyLeagueOfLegends_item(match.item1)}
                                        </div>
                                        <div>
                                            {this.verifyLeagueOfLegends_item(match.item2)}
                                        </div>
                                        <div>
                                            {this.verifyLeagueOfLegends_item(match.item3)}
                                        </div>
                                        <div>
                                            {this.verifyLeagueOfLegends_item(match.item4)}
                                        </div>
                                        <div>
                                            {this.verifyLeagueOfLegends_item(match.item5)}
                                        </div>
                                        <div>
                                            {this.verifyLeagueOfLegends_item(match.item6)}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        )
                    })}
                </div>
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
