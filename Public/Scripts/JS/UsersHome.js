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
             * Game Name of the player
             * @type {string}
             */
            gameName: "",
            /**
             * PUBG's ID of the user
             * @type {string}
             */
            pubgId: "",
            /**
             * PUBG's platform of the user
             * @type {string}
             */
            pubgPlatform: "",
            /**
             * PUBG's username of the user
             * @type {string}
             */
            pubgPlayerName: "",
        };
    }
    /**
     * Retrieving the session's data that is stored as a JSON to be used in the rendering
     */
    retrieveData() {
        fetch("/Users/CurrentUser", {
            method: "GET",
        })
            .then((response) => response.json())
            .then((data) =>
                this.setState({
                    username: data.User.username,
                    mailAddress: data.User.mailAddress,
                    domain: data.User.domain,
                    profilePicture: data.User.profilePicture,
                    lolUsername: data.Account.LeagueOfLegends.gameName,
                    lolRegion: data.Account.LeagueOfLegends.tagLine,
                    riotId: data.Account.LeagueOfLegends
                        .playerUniversallyUniqueIdentifier,
                    pubgId: data.Account.PlayerUnknownBattleGrounds.identifier,
                    pubgPlatform:
                        data.Account.PlayerUnknownBattleGrounds.platform,
                    pubgPlayerName:
                        data.Account.PlayerUnknownBattleGrounds.playerName,
                })
            );
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
            return (
                <a
                    href={`/Users/Profile/${this.state.username}`}
                    class="fa fa-user"
                ></a>
            );
        }
    }
    /**
     * Verifying the state before rendering the link
     * @returns {Application} Component
     */
    verifyAccount_Riot_ID() {
        if (this.state.riotId != null) {
            return (
                <div id="leagueOfLegendsCard">
                    <div>
                        <a
                            href={`/LeagueOfLegends/Home/${this.state.gameName}`}
                        >
                            <img src="/Public/Images/League Of Legends Logo.png" />
                        </a>
                    </div>
                    <div>
                        <div>
                            <img
                                src={`http://ddragon.leagueoflegends.com/cdn/12.22.1/img/profileicon/${this.state.summonerIcon}.png`}
                            />
                            <div>Level {this.state.level}</div>
                            <div>{this.state.gameName}</div>
                        </div>
                        <div>
                            <div>
                                <div>Solo/Duo</div>
                                <div>
                                    <img
                                        src={this.verifyLeagueOfLegends_rank_emblem(
                                            this.state.soloDuoTier
                                        )}
                                    />
                                </div>
                                <div>
                                    <div>
                                        {this.verifyLeagueOfLegends_rank(
                                            this.state.soloDuoTier,
                                            this.state.soloDuoDivision,
                                            this.state.soloDuoLeaguePoints
                                        )}
                                    </div>
                                    <div>Win Rate:</div>
                                    <div
                                        style={{
                                            color: this.verifyLeagueOfLegends_winRate(
                                                this.state.soloDuoWinRate
                                            ),
                                        }}
                                    >{`${this.state.soloDuoWinRate} %`}</div>
                                </div>
                            </div>
                            <div>
                                <div>Flex 5v5</div>
                                <div>
                                    <img
                                        src={this.verifyLeagueOfLegends_rank_emblem(
                                            this.state.flexTier
                                        )}
                                    />
                                </div>
                                <div>
                                    <div>
                                        {this.verifyLeagueOfLegends_rank(
                                            this.state.flexTier,
                                            this.state.flexDivision,
                                            this.state.flexLeaguePoints
                                        )}
                                    </div>
                                    <div>Win Rate:</div>
                                    <div
                                        style={{
                                            color: this.verifyLeagueOfLegends_winRate(
                                                this.state.flexWinRate
                                            ),
                                        }}
                                    >{`${this.state.flexWinRate} %`}</div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div>
                                <div>KDA:</div>
                                <div
                                    style={{
                                        color: this.verifyLeagueOfLegends_kda(
                                            this.state.kdaRatio
                                        ),
                                    }}
                                >
                                    {this.state.kdaRatio}
                                </div>
                            </div>
                            <div>
                                <div>CS/Min:</div>
                                <div
                                    style={{
                                        color: this.verifyLeagueOfLegends_csMin(
                                            this.state.csMin
                                        ),
                                    }}
                                >
                                    {this.state.csMin}
                                </div>
                            </div>
                            <div>
                                <div>VS/Min:</div>
                                <div
                                    style={{
                                        color: this.verifyLeagueOfLegends_vsMin(
                                            this.state.vsMin
                                        ),
                                    }}
                                >
                                    {this.state.vsMin}
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
                    <a href={`/Users/Accounts/${this.state.username}`}>here</a>{" "}
                    to process into adding your account!
                </div>
            );
        }
    }
    /**
     * Retrieving data from Riot Games data center for the user
     */
    retrieveLoL_SummonerData() {
        fetch("/LegendsOfLegends/CurrentSummoner", {
            method: "GET",
        })
            .then((response) => response.json())
            .then((data) =>
                this.setState({
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
                    gameName: data.gameName,
                })
            );
    }
    /**
     * Verifying the ranks of the player
     * @param {string | null} tier
     * @param {string | null} rank
     * @param {number | null} point
     * @returns {string}
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
     * Retrieving data from PUBG Corporations data center for the user
     */
    retrievePUBG_PlayerData() {
        fetch("/PlayerUnknownBattleGrounds/CurrentPlayer", {
            method: "GET",
        })
            .then((response) => response.json())
            .then((data) => console.log(data));
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
        this.retrieveData();
    }
    render() {
        return (
            <header>
                <nav>
                    <div>
                        <a href={`/Users/Home/${this.state.username}`}>
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
        this.retrieveLoL_SummonerData();
        this.retrievePUBG_PlayerData();
    }
    render() {
        return <main>{this.verifyAccount_Riot_ID()}</main>;
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
