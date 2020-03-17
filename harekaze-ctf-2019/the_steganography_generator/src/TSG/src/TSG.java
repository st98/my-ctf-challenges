import java.util.Scanner;

public class TSG {
	public static void main(String[] args) {
		if (args.length < 3) {
			System.err.println("Usage: java -jar tsg.jar <image> <payload> <out>");
			System.exit(1);
		}

		String password = "";
		Scanner scanner = new Scanner(System.in);

		do {
			System.out.print("Please input the password (4 ~ 8 characters): ");
			password = scanner.nextLine();
		} while (password.length() < 4 || password.length() > 8);

		scanner.close();

		Embedder embedder = new Embedder(args[0]);
		embedder.embedFileWithPassword(args[1], password);
		embedder.save(args[2]);
	}
}
