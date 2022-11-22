/**
 * The Application that is going to be rendered in the DOM
 * @type {Component}
 */
class Application extends React.Component {
    /**
     * Renders the components that are being returned
     * @returns {Component[]}
     */
    render() {
        return [<Header />, <Main />, <Footer />];
    }
}
/**
 * The component that is the header
 * @type {Application}
 */
class Header extends Application {
    render() {
        return <header></header>;
    }
}
/**
 * The component that is the main
 * @type {Application}
 */
class Main extends Application {
    render() {
        return <main></main>;
    }
}
/**
 * The component that is the footer
 * @type {Application}
 */
class Footer extends Application {
    render() {
        return <footer></footer>;
    }
}
// Rendering the page
ReactDOM.render(<Application />, document.body);
