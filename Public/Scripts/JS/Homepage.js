/**
 * The Application that is going to be rendered in the DOM
 */
class Application extends React.Component {
    /**
     * Renders the components that are being returned
     */
    render() {
        return [<Header />, <Main />, <Footer />];
    }
}
/**
 * The component that is the header
 */
class Header extends Application {
    render() {
        return <header>1</header>;
    }
}
/**
 * The component that is the main
 */
class Main extends Application {
    render() {
        return <main>2</main>;
    }
}
/**
 * The component that is the footer
 */
class Footer extends Application {
    render() {
        return <footer>3</footer>;
    }
}
// Rendering the page
ReactDOM.render(<Application />, document.body);
