using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Reflection;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace Harekaze15Puzzle
{
    public partial class Form1 : Form
    {
        public Form1()
        {
            InitializeComponent();
        }

        private Random rnd;
        private int[] panels = new int[16];
        private Button[] buttons = new Button[16];
        private int score = 0;

        private void Form1_Load(object sender, EventArgs e)
        {
            for (var y = 0; y < 4; y++)
            {
                for (var x = 0; x < 4; x++)
                {
                    var button = new Button();
                    button.Name = String.Format("{0},{1}", x, y);
                    button.Font = new Font(button.Font.FontFamily, 16);
                    button.Location = new Point(8 + 56 * x, 24 + 56 * y);
                    button.Size = new Size(48, 48);
                    button.TabStop = false;
                    button.Click += Button_Click;

                    this.buttons[this.CalcIndex(x, y)] = button;
                }
            }
            this.Controls.AddRange(this.buttons);
            this.rnd = new Random(typeof(Form1).GetMethods(BindingFlags.DeclaredOnly | BindingFlags.Instance | BindingFlags.NonPublic)
                                      .Select(x => CRC32.Compute(x.GetMethodBody().GetILAsByteArray()))
                                      .Aggregate((x, y) => x ^ y));
            this.InitializePanels();
        }

        private void Button_Click(object sender, EventArgs e)
        {
            var button = (Button) sender;
            var pos = button.Name.Split(',').Select(Int32.Parse).ToList();
            var x = pos[0];
            var y = pos[1];

            if (x > 0 && this.panels[this.CalcIndex(x - 1, y)] == -1)
            {
                this.Swap(this.panels, this.CalcIndex(x, y), this.CalcIndex(x - 1, y));
            } else if (x < 3 && this.panels[this.CalcIndex(x + 1, y)] == -1)
            {
                this.Swap(this.panels, this.CalcIndex(x, y), this.CalcIndex(x + 1, y));
            } else if (y > 0 && this.panels[this.CalcIndex(x, y - 1)] == -1)
            {
                this.Swap(this.panels, this.CalcIndex(x, y), this.CalcIndex(x, y - 1));
            } else if (y < 3 && this.panels[this.CalcIndex(x, y + 1)] == -1)
            {
                this.Swap(this.panels, this.CalcIndex(x, y), this.CalcIndex(x, y + 1));
            }

            if (this.IsPuzzleSolved())
            {
                this.score++;
                this.label2.Text = String.Format("Score: {0} / 1000", this.score);
                this.ShufflePanels();
            }

            if (this.score == 1000)
            {
                FlagGenerator.ShowFlag(this.rnd);
            }

            this.UpdateButtons();
        }

        private void Swap(int[] a, int i, int j)
        {
            var tmp = a[i];
            a[i] = a[j];
            a[j] = tmp;
        }

        private int CalcIndex(int x, int y)
        {
            return y * 4 + x % 4;
        }

        private void InitializePanels()
        {
            for (var i = 0; i < 15; i++)
            {
                this.panels[i] = i + 1;
            }
            this.panels[15] = -1;

            this.ShufflePanels();
            this.UpdateButtons();
        }

        private void ShufflePanels()
        {
            for (var i = 15; i > 0; i--)
            {
                var j = rnd.Next(16);
                this.Swap(this.panels, i, j);
            }

            if (!this.IsPuzzleSolvable())
            {
                this.FlipPanels();
            }
        }

        private void FlipPanels()
        {
            var newPanels = new int[16];
            for (int y = 0; y < 4; y++)
            {
                for (int x = 0; x < 4; x++)
                {
                    newPanels[this.CalcIndex(3 - x, y)] = this.panels[this.CalcIndex(x, y)];
                }
            }
            this.panels = newPanels;
        }

        private void UpdateButtons()
        {
            for (var i = 0; i < 16; i++)
            {
                if (this.panels[i] == -1)
                {
                    this.buttons[i].Text = "";
                    this.buttons[i].Enabled = false;
                } else
                {
                    this.buttons[i].Text = this.panels[i].ToString();
                    this.buttons[i].Enabled = true;
                }
            }
        }

        private bool IsPuzzleSolvable()
        {
            var sum = 0;
            for (var i = 0; i < 16; i++)
            {
                var panel = this.panels[i];
                if (panel == -1)
                {
                    continue;
                }
                sum += this.panels.Skip(i).Where(x => x != -1 && panel > x).ToArray().Length;
            }
            sum += Array.IndexOf(this.panels, -1) / 4 + 1;
            return sum % 2 == 0;
        }

        private bool IsPuzzleSolved()
        {
            for (var i = 0; i < 15; i++)
            {
                if (this.panels[i] != i + 1)
                {
                    return false;
                }
            }
            return true;
        }
    }
}
