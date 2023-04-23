/**
 * The Application that is going to be rendered in the DOM
 */
class Application extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            User: {
                username: "",
                mailAddress: "",
                profilePicture: "",
            },
            Accounts: {
                LeagueOfLegends: {
                    gameName: "",
                },
                PlayerUnknownBattleGrounds: {
                    playerName: "",
                },
            },
        };
    }
    /**
     * Retrieving the session's data that is stored as a JSON to be used in the rendering
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
                        mailAddress: data.User.mailAddress,
                        profilePicture: data.User.profilePicture,
                    },
                    Accounts: {
                        LeagueOfLegends: {
                            gameName: data.Account.LeagueOfLegends.gameName,
                        },
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
     * Verifying the state before rendering the link
     * @returns {object}
     */
    verifyUser_profilePicture() {
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
     * Verifying whether there is a profile picture
     * @returns {object}
     */
    verifyProfilePicture() {
        if (this.state.User.profilePicture != null) {
            return <img src={this.state.User.profilePicture} />;
        } else {
            return <i class="fa fa-user"></i>;
        }
    }
    /**
     * Verifying the state before applying style
     * @returns {object|null}
     */
    verifyAccount_Riot_ID_styling() {
        if (this.state.Accounts.LeagueOfLegends.gameName == null) {
            return {
                display: "none",
            };
        }
    }
    /**
     * Verifying the state before applying style
     * @returns {object|null}
     */
    verifyAccount_PUBG_ID_styling() {
        if (this.state.Accounts.PlayerUnknownBattleGrounds.playerName == null) {
            return {
                display: "none",
            };
        }
    }
    /**
     * Rendering the application
     * @returns {Application[]}
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
    componentDidMount() {
        this.getUser();
    }
    render() {
        return (
            <main>
                <nav>
                    <div>
                        <a
                            href={`/Users/Edit/Profile/${this.state.User.username}`}
                            class="fas fa-edit"
                        ></a>
                    </div>
                    <div>
                        <a
                            href={`/Users/Accounts/${this.state.User.username}`}
                            class="fa fa-user"
                        ></a>
                    </div>
                    <div>
                        <a
                            href={`/Users/Security/${this.state.User.username}`}
                            class="fa-solid fa-user-shield"
                        ></a>
                    </div>
                </nav>
                <div>
                    <header>
                        <div id="profilePicture">
                            {this.verifyProfilePicture()}
                        </div>
                        <div id="username">{this.state.User.username}</div>
                    </header>
                    <description>
                        <div id="mailAddress">
                            <label>Mail Address:</label>
                            <div>{this.state.User.mailAddress}</div>
                        </div>
                        <div
                            id="lolUsername"
                            style={this.verifyAccount_Riot_ID_styling()}
                        >
                            <label>League of Legends's Username:</label>
                            <div>
                                {this.state.Accounts.LeagueOfLegends.gameName}
                            </div>
                        </div>
                        <div
                            id="pubgUsername"
                            style={this.verifyAccount_PUBG_ID_styling()}
                        >
                            <label>
                                Player Unknown Battle Grounds's Username:
                            </label>
                            <div>
                                {
                                    this.state.Accounts
                                        .PlayerUnknownBattleGrounds.playerName
                                }
                            </div>
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
    render() {
        return <footer>Parkinston</footer>;
    }
}
// Rendering the page
ReactDOM.render(<Application />, document.body);
