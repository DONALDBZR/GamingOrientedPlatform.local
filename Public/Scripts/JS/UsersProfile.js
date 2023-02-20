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
             * User's PUBG's account's ID
             * @type {string}
             */
            pubgId: "",
            /**
             * User's PUBG's username
             * @type {string}
             */
            pubgUsername: "",
            /**
             * User's PUBG's platform
             * @type {string}
             */
            pubgPlatform: "",
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
                    pubgUsername:
                        data.Account.PlayerUnknownBattleGrounds.playerName,
                    pubgPlatform:
                        data.Account.PlayerUnknownBattleGrounds.platform,
                })
            );
    }
    /**
     * Verifying the state before rendering the link
     * @returns {Application} Component
     */
    verifyUser_profilePicture() {
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
     * Verifying whether there is a profile picture
     * @returns {ProfilePicture}
     */
    verifyProfilePicture() {
        if (this.state.profilePicture != null) {
            return <img src={this.state.profilePicture} />;
        } else {
            return <i class="fa fa-user"></i>;
        }
    }
    /**
     * Verifying the state before applying style
     */
    verifyAccount_Riot_ID_styling() {
        if (this.state.riotId == null) {
            return {
                display: "none",
            };
        }
    }
    /**
     * Verifying the state before applying style
     */
    verifyAccount_PUBG_ID_styling() {
        if (this.state.pubgId == null) {
            return {
                display: "none",
            };
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
    /**
     * Methods to be run as soon as the component is mounted
     */
    componentDidMount() {
        this.retrieveData();
    }
    /**
     * @returns {Header} Component
     */
    render() {
        return (
            <header>
                <nav>
                    <div>
                        <a href={`/Users/Home/${this.state.username}`}>
                            Parkinston
                        </a>
                    </div>
                    <div>{this.verifyUser_profilePicture()}</div>
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
    /**
     * @returns {Main} Component
     */
    render() {
        return (
            <main>
                <nav>
                    <div>
                        <a
                            href={`/Users/Edit/Profile/${this.state.username}`}
                            class="fas fa-edit"
                        ></a>
                    </div>
                    <div>
                        <a
                            href={`/Users/Accounts/${this.state.username}`}
                            class="fa fa-user"
                        ></a>
                    </div>
                    <div>
                        <a
                            href={`/Users/Security/${this.state.username}`}
                            class="fa-solid fa-user-shield"
                        ></a>
                    </div>
                </nav>
                <div>
                    <header>
                        <div id="profilePicture">
                            {this.verifyProfilePicture()}
                        </div>
                        <div id="username">{this.state.username}</div>
                    </header>
                    <description>
                        <div id="mailAddress">
                            <label>Mail Address:</label>
                            <div>{this.state.mailAddress}</div>
                        </div>
                        <div
                            id="lolUsername"
                            style={this.verifyAccount_Riot_ID_styling()}
                        >
                            <label>League of Legends's Username:</label>
                            <div>{this.state.lolUsername}</div>
                        </div>
                        <div
                            id="pubgUsername"
                            style={this.verifyAccount_PUBG_ID_styling()}
                        >
                            <label>
                                Player Unknown Battle Grounds's Username:
                            </label>
                            <div>{this.state.pubgUsername}</div>
                        </div>
                    </description>
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
    /**
     * Methods to be run as soon as the component is mounted
     */
    componentDidMount() {
        this.retrieveData();
    }
    /**
     * @returns {Footer} Component
     */
    render() {
        return <footer>Parkinston</footer>;
    }
}
// Rendering the page
ReactDOM.render(<Application />, document.body);
