import java.awt.image.BufferedImage;
import java.io.*;
import java.nio.file.*;
import java.util.*;

import javax.imageio.ImageIO;

public class Embedder {
	final byte[] MAGIC_NUMBER = {127, 115, 116, 101, 103, 97, 110, 111};
	BufferedImage img;
	byte[] payload;
	
	public Embedder(String imagePath) {
		try {
			img = ImageIO.read(new File(imagePath));
		} catch (IOException e) {
			System.err.println("Unable to load " + imagePath);
			System.exit(1);
		}
	}
	
	public void embedFileWithPassword(String payloadPath, String password) {
		ByteArrayOutputStream bos = new ByteArrayOutputStream();
		
		try {
			bos.write(MAGIC_NUMBER);
			bos.write(Files.readAllBytes(Paths.get(payloadPath)));
		} catch (IOException e) {
			System.err.println("Unable to load " + payloadPath);
			System.exit(1);
		}
		
		payload = bos.toByteArray();
		
		if (payload.length > img.getHeight() || img.getWidth() < 8) {
			System.err.println("Given image is too small");
			System.exit(1);
		}
		
		try {
			for (int i = 0; i < payload.length; i++) {
				int seed = password.codePointAt(i % password.length());
				seed ^= i * i * password.codePointAt((i + password.length() - 1) % password.length());
				Random rnd = new Random(seed);
				
				Set<Integer> used = new HashSet<Integer>();
				
				for (int j = 0; j < 8; j++) {
					int x = rnd.nextInt(img.getWidth());
					do {
						x = (x + 1) % img.getWidth();
					} while (used.contains(x));
					used.add(x);
					
					int pixel = img.getRGB(x, i);
					pixel &= ~(1 << 16);
					if ((payload[i] & (1 << j)) != 0) {
						pixel |= 1 << 16;
					}

					img.setRGB(x, i, pixel);
				}
			}
		} catch (ArrayIndexOutOfBoundsException e) {
			System.err.println("Given image is too small");
			System.exit(1);
		}
	}
	
	public void save(String outPath) {
		try {
			ImageIO.write(img, "png", new File(outPath));
		} catch (IOException e) {
			System.err.println("Unable to save to " + outPath);
			System.exit(1);
		}
		
		System.out.println("Success!");
	}
}