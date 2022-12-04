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
        };
    }
    /**
     * Retrieving the session's data that is stored as a JSON to be used in the rendering
     */
    retrieveData() {
        Promise.all([
            fetch("/Users/CurrentUser",
                {
                    method: "GET"
                }),
            fetch("/LegendsOfLegends/CurrentSummoner",
                {
                    method: "GET"
                })
        ])
            .then(([user, summoner]) => {
                const UserJSON = user.json()
                const SummonerJSON = summoner.json()
                return [UserJSON, SummonerJSON]
            })
            .then((data) => {
                data[0].then((user) => this.setState({
                    username: user.User.username,
                    mailAddress: user.User.mailAddress,
                    domain: user.User.domain,
                    profilePicture: user.User.profilePicture,
                    lolUsername: user.Account.LeagueOfLegends.gameName,
                    lolRegion: user.Account.LeagueOfLegends.tagLine,
                    riotId: user.Account.LeagueOfLegends.playerUniversallyUniqueIdentifier,
                }))
                data[1].then((summoner) => this.setState({
                    level: summoner.summonerLevel,
                    summonerIcon: summoner.profileIconId,
                    soloDuoTier: summoner.soloDuoTier,
                    soloDuoDivision: summoner.soloDuoRank,
                    soloDuoLeaguePoints: summoner.soloDuoLeaguePoints,
                    flexTier: summoner.flexTier,
                    flexDivision: summoner.flexRank,
                    flexLeaguePoints: summoner.flexLeaguePoints,
                }))
            });
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
     * Methods to be run as soon as the component is mounted
     */
    componentDidMount() {
        this.retrieveData();
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
                        </div>
                        <div>
                            <div>Flex 5v5</div>
                            <div>
                                <img src={`/Public/Images/Ranks/Emblem_${this.state.flexTier}.png`} />
                            </div>
                            <div>{`${this.state.flexTier} ${this.state.flexDivision} - ${this.state.flexLeaguePoints} LP`}</div>
                        </div>
                    </div>
                    <div>Level {this.state.level}</div>
                    <div>Win Rate</div>
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