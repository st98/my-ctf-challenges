using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace Harekaze15Puzzle
{
    class FlagGenerator
    {
        private static byte[] flag = { 159, 51, 130, 252, 110, 200, 27, 137, 249, 14, 37, 7, 160, 117, 204, 162, 141, 200, 56, 24, 95, 18, 122, 75, 35, 207, 252, 84, 199, 50, 68, 224, 15, 56, 191, 176, 26, 207, 97, 28, 164, 69, 123, 129, 72, 121 };
        public static void ShowFlag(Random rnd)
        {
            for (var i = 0; i < flag.Length; i++)
            {
                flag[i] ^= (byte)rnd.Next(256);
            }
            MessageBox.Show(String.Format("Congratulations! The flag is {0}", Encoding.ASCII.GetString(flag)));
        }
    }
}
