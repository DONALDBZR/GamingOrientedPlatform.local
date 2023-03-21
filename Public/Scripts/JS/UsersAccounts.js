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
            User: {
                username: "",
                mailAddress: "",
                profilePicture: "",
            },
            System: {
                status: 0,
                message: "",
                url: "",
            },
            Accounts: {
                LeagueOfLegends: {
                    gameName: "",
                    tagLine: "",
                    Regions: [],
                },
                PlayerUnknownBattleGrounds: {
                    playerName: "",
                    platform: "",
                },
            },
        };
    }
    /**
     * Retrieving the data from the server
     */
    retrieveData() {
        this.getCurrentUser();
        this.getRegions();
    }
    /**
     * Acessing the data of the current user
     */
    getCurrentUser() {
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
                            tagLine: data.Account.LeagueOfLegends.tagLine,
                        },
                        PlayerUnknownBattleGrounds: {
                            platform:
                                data.Account.PlayerUnknownBattleGrounds
                                    .platform,
                            playerName:
                                data.Account.PlayerUnknownBattleGrounds
                                    .playerName,
                        },
                    },
                })
            );
    }
    /**
     * Retrieving all the regions for League of Legends
     */
    getRegions() {
        fetch("/LeagueOfLegends/Regions", {
            method: "GET",
        })
            .then((response) => response.json())
            .then((data) =>
                this.setState({
                    Accounts: {
                        LeagueOfLegends: {
                            Regions: data,
                        },
                    },
                })
            );
    }
    /**
     * Verifying the state before rendering the link
     * @returns {Application} Component
     */
    verifyState() {
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
            Accounts: {
                ...previous.Accounts,
                [name]: value,
            },
        }));
    }
    /**
     * Handling the form submission
     * @param {Event} event
     */
    handleSubmit(event) {
        const delay = 2150;
        event.preventDefault();
        fetch(`/User/Accounts/${this.state.User.username}`, {
            method: "POST",
            body: JSON.stringify({
                LeagueOfLegends: {
                    gameName: this.state.Accounts.LeagueOfLegends.gameName,
                    tagline: this.state.Accounts.LeagueOfLegends.tagLine,
                },
                PlayerUnknownBattleGrounds: {
                    playerName:
                        this.state.Accounts.PlayerUnknownBattleGrounds
                            .playerName,
                    platform:
                        this.state.Accounts.PlayerUnknownBattleGrounds.platform,
                },
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
                        message: data.message,
                        url: data.url,
                    },
                })
            )
            .then(() => this.redirector(delay));
    }
    /**
     * Handling the response from the server
     * @returns {string}
     */
    handleResponseColor() {
        if (this.state.System.status == 0) {
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
        if (this.state.System.status == 0) {
            return "71%";
        } else {
            return "180%";
        }
    }
    /**
     * Verifying whether there is a profile picture
     * @returns {Application} Component
     */
    verifyProfilePicture() {
        if (this.state.User.profilePicture != null) {
            return <img src={this.state.User.profilePicture} />;
        } else {
            return <i class="fa fa-user"></i>;
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
        this.getCurrentUser();
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
                    <div>{this.verifyState()}</div>
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
    render() {
        return (
            <main>
                <form method="POST" onSubmit={this.handleSubmit.bind(this)}>
                    <div id="label">Accounts Form</div>
                    <div>League of Legends</div>
                    <input
                        type="text"
                        name="gameName"
                        placeholder="League Of Legends Username"
                        value={this.state.Accounts.LeagueOfLegends.gameName}
                        onChange={this.handleChange.bind(this)}
                    />
                    <select
                        name="tagLine"
                        onChange={this.handleChange.bind(this)}
                        value={this.state.Accounts.LeagueOfLegends.tagLine}
                    >
                        <option value=""></option>
                        <option value="BR">BR</option>
                        <option value="EUN">EUNE</option>
                        <option value="EUW">EUW</option>
                        <option value="JP">JP</option>
                        <option value="KR">KR</option>
                        <option value="LA">LA</option>
                        <option value="NA">NA</option>
                        <option value="OC">OC</option>
                        <option value="TR">TR</option>
                        <option value="RU">RU</option>
                        <option value="PH">PH</option>
                        <option value="SG">SG</option>
                        <option value="TH">TH</option>
                        <option value="TW">TW</option>
                        <option value="VN">VN</option>
                    </select>
                    <div>PUBG</div>
                    <input
                        type="text"
                        name="playerName"
                        placeholder="PUBG Username"
                        value={
                            this.state.Accounts.PlayerUnknownBattleGrounds
                                .playerName
                        }
                        onChange={this.handleChange.bind(this)}
                    />
                    <select
                        name="platform"
                        onChange={this.handleChange.bind(this)}
                        value={
                            this.state.Accounts.PlayerUnknownBattleGrounds
                                .platform
                        }
                    >
                        <option value=""></option>
                        <option value="kakao">Kakao</option>
                        <option value="stadia">Stadia</option>
                        <option value="steam">Steam</option>
                        <option value="tournament">Tournaments</option>
                        <option value="psn">PSN</option>
                        <option value="xbox">Xbox</option>
                    </select>
                    <div id="button">
                        <button>Change</button>
                    </div>
                    <div id="response">
                        <h1
                            style={{
                                color: this.handleResponseColor(),
                                fontSize: this.handleResponseFontSize(),
                            }}
                        >
                            {this.state.System.message}
                        </h1>
                    </div>
                </form>
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
