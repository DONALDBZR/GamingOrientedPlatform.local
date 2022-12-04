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
        };
    }
    /**
     * Retrieving the session's data that is stored as a JSON to be used in the rendering
     */
    retrieveData() {
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
                riotId: data.Account.LeagueOfLegends.playerUniversallyUniqueIdentifier
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
     * Methods to be run as soon as the component is mounted
     */
    componentDidMount() {
        this.retrieveData();
    }
    /**
     * Verifying the state before rendering the link
     * @returns {Application} Component
     */
    verifyAccount_Riot_ID() {
        if (this.state.riotId != null) {
            return (
                <a href={`/LeagueOfLegends/Home/${this.state.lolUsername}`}>
                    <img src="/Public/Images/(12).ico" />
                </a>
            );
        } else {
            return (
                <div>
                    You should add your account for League of Legends before having accessed to the required content.  You can click <a href={`/Users/Accounts/${this.state.username}`}>here</a> to process into adding your account!
                </div>
            );
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
 * The component that is the main
 */
class Main extends Application {
    constructor(props) {
        super(props);
    }
    render() {
        return (
            <main>
                <div>
                    {this.verifyAccount_Riot_ID()}
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
// Rendering the page
ReactDOM.render(<Application />, document.body);
