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
            summoner: 0,
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
     * Retrieving data from Riot Games data center
     */
    retrieveLoLData() {
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
                <NavigationBar />
            </header>
        );
    }
}
/**
 * The navigation bar component
 */
class NavigationBar extends Header {
    constructor(props) {
        super(props);
    }
    /**
     * Methods to be run as soon as the component is mounted
     */
    componentDidMount() {
        this.retrieveSessionData();
    }
    render() {
        return (
            <nav>
                <div>
                    <a href={`/Users/Home/${this.state.username}`}>Parkinston</a>
                </div>
                <ProfileLink />
                <div>
                    <a href="/Sign-Out" class="fa fa-sign-out"></a>
                </div>
            </nav>
        );
    }
}
/**
 * The component which will render the profile picture of the user
 */
class ProfileLink extends NavigationBar {
    constructor(props) {
        super(props);
    }
    render() {
        return (
            <div>{this.verifyUser_username()}</div>
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
        this.retrieveLoLData();
    }
    render() {
        return (
            <main>
                <header>
                    <div>
                        <img src={`http://ddragon.leagueoflegends.com/cdn/12.22.1/img/profileicon/${this.state.summonerIcon}.png`} />
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
                    <div>Level {this.state.level}</div>
                    <div>KDA</div>
                    <div>CS/Min</div>
                    <div>VS/Min</div>
                </header>
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
